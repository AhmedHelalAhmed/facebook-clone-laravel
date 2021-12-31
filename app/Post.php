<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];// disable mass assignment

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
