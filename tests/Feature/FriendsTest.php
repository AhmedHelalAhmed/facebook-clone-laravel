<?php

namespace Tests\Feature;

use App\Friend;
use App\User;
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
}
