<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'legal_data', 'address_id', 'processed',];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address()
    {
        return $this->hasOne('App\Models\Address', 'id', 'address_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function routes()
    {
        return $this->belongsToMany('App\Models\Route', 'routes_stores', 'store_id', 'route_id')
            ->withPivot('distance');
    }

    /**
     * @param int $providerId
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function availableRoutes(int $providerId)
    {
        return $this->routes()->where('routes.user_id', '=',$providerId);
    }
}
