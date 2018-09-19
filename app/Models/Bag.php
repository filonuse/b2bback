<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bag extends Model
{
    protected $fillable = [
        'customer_id', 'provider_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods')
            ->withPivot('quantity');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne('App\Models\User', 'id', 'provider_id');
    }
}
