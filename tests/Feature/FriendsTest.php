<?php

namespace Tests\Feature;

use App\Actions\StoreFriendRequestAction;
use App\DataTransferObjects\FriendDTO;
use App\Friend;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_request()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();

        $response = $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id
        ])->assertOk();

        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest);
        $this->assertEquals($anotherUser->id, $friendRequest->friend_id);
        $this->assertEquals($user->id, $friendRequest->user_id);

        $response->assertJson([
            'data' => [
                'type' => 'friend_request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null
                ]
            ],
            'links' => [
                'self' => url('/users/' . $anotherUser->id)
            ]
        ]);
    }

    /** @test */
    public function only_valid_users_can_be_friend_requested()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/friend-request', [
            'friend_id' => 123
        ])->assertNotFound();

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'code' => Response::HTTP_NOT_FOUND,
                'title' => 'User Not Found',
                'detail' => 'Unable to locate the user with the given information.'
            ]
        ]);
    }

    /** @test */
    public function friend_request_can_be_accepted()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id
        ])->assertOk();

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 1,
            ])->assertOk();

        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'friend_request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans()
                ]
            ],
            'links' => [
                'self' => url('/users/' . $anotherUser->id)
            ]
        ]);
    }

    /** @test */
    public function only_valid_friend_requests_can_be_accepted()
    {
        $anotherUser = factory(User::class)->create();
        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => 123,
                'status' => 1,
            ])->assertNotFound();
        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'code' => Response::HTTP_NOT_FOUND,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information.'
            ]
        ]);
    }

    /** @test */
    public function only_the_recipient_can_accept_a_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id
        ])->assertOk();


        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 1,
            ])->assertNotFound();

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => Response::HTTP_NOT_FOUND,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information.'
            ]
        ]);
    }

    /** @test */
    public function a_friend_id_is_required_for_friend_requests()
    {
        $response = $this->actingAs($user = factory(User::class)->create(), 'api')
            ->post('/api/friend-request', [
                'friend_id' => ''
            ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArrayHasKey('friend_id', $response->json('errors.meta'));
    }

    /** @test */
    public function a_user_id_and_status_is_required_for_friend_request_response()
    {
        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => '',
                'status' => '',
            ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArrayHasKey('user_id', $response->json('errors.meta'));
        $this->assertArrayHasKey('status', $response->json('errors.meta'));
    }

    /** @test */ // TODO refactor into convention
    public function a_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $storeFriendRequestAction = app(StoreFriendRequestAction::class);
        $friendRequest = $storeFriendRequestAction(
            new FriendDTO([
                'userId' => $user->id,
                'friendId' => $anotherUser->id,
                'confirmedAt' => now()->subDay(),
                'status' => 1,
            ])
        );
        $this->get('/api/users/' . $anotherUser->id)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /** @test */ // TODO refactor into convention
    public function a_inverse_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $storeFriendRequestAction = app(StoreFriendRequestAction::class);
        $friendRequest = $storeFriendRequestAction(
            new FriendDTO([
                'friendId' => $user->id,
                'userId' => $anotherUser->id,
                'confirmedAt' => now()->subDay(),
                'status' => 1,
            ])
        );
        $this->get('/api/users/' . $anotherUser->id)
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /** @test */ // TODO refactor into convention
    public function friend_request_can_be_ignored()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id
        ])->assertOk();

        $response = $this->actingAs($anotherUser, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertStatus(Response::HTTP_NO_CONTENT);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest);
        $response->assertNoContent();
    }

    /** @test */ // TODO refactor into convention
    public function only_the_recipient_can_ignore_a_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id
        ])->assertOk();


        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertNotFound();

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => Response::HTTP_NOT_FOUND,
                'title' => 'Friend Request Not Found',
                'detail' => 'Unable to locate the friend request with the given information.'
            ]
        ]);
    }

    /** @test */ // TODO refactor into convention
    public function a_user_id_is_required_for_ignoring_a_friend_request_response()
    {
        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => '',
            ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArrayHasKey('user_id', $response->json('errors.meta'));
    }

}
