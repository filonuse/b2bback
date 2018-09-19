<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'directions', 'time_start', 'time_finish', 'max_deviation', 'activated', 'processed',
    ];

    protected $dates = ['deleted_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addresses()
    {
        return $this->belongsToMany('App\Models\Address', 'route_addresses', 'route_id', 'address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany('App\Models\Store', 'routes_stores', 'route_id', 'store_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviations()
    {
        return $this->hasMany('App\Models\RouteDeviation', 'route_id', 'id');
    }
}
