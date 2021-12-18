<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PostController extends Controller
{
    public function store()
    {
        return response([], Response::HTTP_CREATED);
    }
}
