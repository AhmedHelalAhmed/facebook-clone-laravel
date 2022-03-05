<?php

namespace App\Validators;

class StoreFriendRequestResponseValidator
{
    public function validate(array $attributes): array
    {
        return validator($attributes,
            [
                'user_id' => 'required',
                'status' => 'required',
            ]
        )->validate();
    }
}
