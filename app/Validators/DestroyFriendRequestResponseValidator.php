<?php

namespace App\Validators;

class DestroyFriendRequestResponseValidator
{
    public function validate(array $attributes): array
    {
        return validator($attributes,
            [
                'user_id' => 'required'
            ]
        )->validate();
    }
}
