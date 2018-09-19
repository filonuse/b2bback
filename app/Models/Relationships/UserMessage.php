<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    protected $fillable = ['user_id', 'message_id', 'read_at'];

    public $timestamps = false;

    protected $dates = ['read_at'];
}
