<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class ValidationErrorException extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param Request $request
     * @return Response
     */
    public function render($request)
    {
        return response()
            ->json(
                [
                    'errors' => [
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'title' => 'Validation Error',
                        'detail' => 'You request is malformed or missing fields.',
                        'meta' => json_decode($this->getMessage()),
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY
            );
    }
}
