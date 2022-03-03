<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\User;
use App\Validators\FriendRequestValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class FriendRequestController extends Controller
{
    /**
     * @throws UserNotFoundException
     */
    public function store()
    {
        $data = (new FriendRequestValidator())->validate(request()->all());

        try {
            User::query()
                ->findOrFail(Arr::get($data, 'friend_id'))
                ->friends()
                ->attach(auth()->user());
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new UserNotFoundException;
        }


        return new FriendResource(
            Friend::query()
                ->where('user_id', auth()->id())
                ->where('friend_id', Arr::get($data, 'friend_id'))
                ->first()
        );
    }
}
