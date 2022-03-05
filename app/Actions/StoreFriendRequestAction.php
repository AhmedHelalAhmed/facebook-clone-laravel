<?php

namespace App\Actions;

use App\DataTransferObjects\FriendDTO;
use App\Friend;

class StoreFriendRequestAction
{
    public function __invoke(FriendDTO $friendDTO)
    {
        Friend::query()->create($friendDTO->toArray());
    }
}
