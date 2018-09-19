<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{
    protected $table = 'users_categories';

    protected $fillable = ['name', 'parent_id'];

    public $timestamps = false;
}
