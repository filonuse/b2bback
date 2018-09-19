<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteDeviation extends Model
{
    protected $table = 'route_deviations';

    protected $fillable = [
        'route_id', 'distance', 'price', 'percent',
    ];

    public $timestamps = false;
}
