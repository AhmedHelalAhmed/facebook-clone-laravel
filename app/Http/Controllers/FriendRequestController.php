<?php

namespace App\Http\Controllers;

use App\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\User;
use App\Validators\FriendRequestValidator;
use Illuminate\Support\Arr;

class FriendRequestController extends Controller
{
    public function store()
    {
        $data = (new FriendRequestValidator())->validate(request()->all());

        User::query()
            ->find(Arr::get($data, 'friend_id'))
            ->friends()
            ->attach(auth()->user());

        return new FriendResource(
            Friend::query()
                ->where('user_id', auth()->id())
                ->where('friend_id', Arr::get($data, 'friend_id'))
                ->first()
        );
    }
}
