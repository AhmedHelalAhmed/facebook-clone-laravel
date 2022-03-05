<?php

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class FriendDTO extends DataTransferObject
{
    public int $userId;
    public int $friendId;
    public ?string $confirmedAt;
    public ?int $status;

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'friend_id' => $this->friendId,
            'confirmed_at' => $this->confirmedAt,
            'status' => $this->status,
        ];
    }
}
