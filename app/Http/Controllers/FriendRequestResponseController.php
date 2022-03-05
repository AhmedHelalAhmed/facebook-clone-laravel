<?php

namespace App\Http\Controllers;

use App\Exceptions\FriendRequestNotFoundException;
use App\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\Validators\DestroyFriendRequestResponseValidator;
use App\Validators\StoreFriendRequestResponseValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class FriendRequestResponseController extends Controller
{
    public function store()
    {
        $data = (new StoreFriendRequestResponseValidator)->validate(request()->all());
        try {
            $friendRequest = Friend::query()
                ->where('user_id', Arr::get($data, 'user_id'))
                ->where('friend_id', auth()->id())
                ->firstOrFail();
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new FriendRequestNotFoundException;
        }

        $friendRequest->update(array_merge(
            $data,
            [
                'confirmed_at' => now()
            ]
        ));

        return new FriendResource($friendRequest);
    }

    /**
     * TODO refactor into convention
     * @return \Illuminate\Http\JsonResponse
     * @throws FriendRequestNotFoundException
     */
    public function destroy()
    {
        $data = (new DestroyFriendRequestResponseValidator)->validate(request()->all());
        try {
            Friend::query()
                ->where('user_id', Arr::get($data, 'user_id'))
                ->where('friend_id', auth()->id())
                ->firstOrFail()
                ->delete();
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new FriendRequestNotFoundException;
        }
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
