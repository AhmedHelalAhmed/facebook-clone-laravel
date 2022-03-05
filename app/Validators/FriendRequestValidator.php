<?php

namespace App\Validators;

use App\Exceptions\UserNotFoundException;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class FriendRequestValidator
{
    public function validate(array $attributes): array
    {
        $data = validator($attributes, ['friend_id' => ['required']])->validate();

        try {
            User::query()->findOrFail(Arr::get($attributes, 'friend_id'));
        } catch (ModelNotFoundException $modelNotFoundException) {
            throw new UserNotFoundException;
        }

        return $data;
    }
}
