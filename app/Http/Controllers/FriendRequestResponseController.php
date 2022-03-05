<?php

namespace App\Http\Controllers;

use App\Exceptions\FriendRequestNotFoundException;
use App\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\Validators\FriendRequestResponseValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class FriendRequestResponseController extends Controller
{
    public function store()
    {
        $data = (new FriendRequestResponseValidator)->validate(request()->all());
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
}
