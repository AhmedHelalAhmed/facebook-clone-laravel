<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class FriendRequestNotFoundException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()
            ->json(
                [
                    'errors' => [
                        'code' => Response::HTTP_NOT_FOUND,
                        'title' => 'Friend Request Not Found',
                        'detail' => 'Unable to locate the friend request with the given information.'
                    ]
                ], Response::HTTP_NOT_FOUND
            );
    }
}
