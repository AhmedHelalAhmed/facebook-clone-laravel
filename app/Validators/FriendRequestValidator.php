<?php

namespace App\Validators;

class FriendRequestValidator
{
    public function validate(array $attributes): array
    {
        return validator($attributes,
            [
                'friend_id' => 'required',
            ]
        )->validate();
    }
}
