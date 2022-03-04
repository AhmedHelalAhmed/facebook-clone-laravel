<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\Validators\FriendRequestResponseValidator;
use Illuminate\Support\Arr;

class FriendRequestResponseController extends Controller
{
    public function store()
    {
        $data = (new FriendRequestResponseValidator)->validate(request()->all());
        $friendRequest = Friend::query()
            ->where('user_id', Arr::get($data, 'user_id'))
            ->where('friend_id', auth()->id())
            ->firstOrFail();

        $friendRequest->update(array_merge(
            $data,
            [
                'confirmed_at' => now()
            ]
        ));

        return new FriendResource($friendRequest);
    }
}
