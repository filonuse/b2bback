<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class RouteStore extends Model
{
    protected $table = 'routes_stores';

    protected $fillable = ['route_id', 'store_id', 'distance'];

    public $timestamps = false;
}
