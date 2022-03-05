<?php

namespace App\Http\Controllers;

use App\Actions\StoreFriendRequestAction;
use App\DataTransferObjects\FriendDTO;
use App\Exceptions\UserNotFoundException;
use App\Friend;
use App\Http\Resources\Friend as FriendResource;
use App\Validators\FriendRequestValidator;
use Illuminate\Support\Arr;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class FriendRequestController extends Controller
{
    /**
     * @throws UserNotFoundException
     * @throws UnknownProperties
     */
    public function store(StoreFriendRequestAction $friendRequestAction)
    {
        $data = (new FriendRequestValidator())->validate(request()->all());


        $friendRequestAction(new FriendDTO([
            'friendId' => Arr::get($data, 'friend_id'),
            'userId' => auth()->id(),
        ]));


        return new FriendResource(
            Friend::query()
                ->where('user_id', auth()->id())
                ->where('friend_id', Arr::get($data, 'friend_id'))
                ->first()
        );
    }
}
