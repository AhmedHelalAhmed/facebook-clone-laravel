<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserNotFoundException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request)
    {
        return response()
            ->json(
                [
                    'errors' => [
                        'code' => Response::HTTP_NOT_FOUND,
                        'title' => 'User Not Found',
                        'detail' => 'Unable to locate the user with the given information.'
                    ]
                ], Response::HTTP_NOT_FOUND
            );
    }
}
