<?php

namespace App\Models;

use App\Traits\ReviewRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use SoftDeletes, ReviewRelation;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand',
        'description',
        'quantity_actual',
        'quantity_reserve',
        'quantity_available',
        'price',
        'article',
        'country',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get all of the goods' images.
     */
    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imagetable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discounts()
    {
        return $this->hasMany('App\Models\Discount', 'provider_id', 'user_id');
    }

    /**
     * @param $userId
     * @return int|null
     */
    public function discount($userId)
    {
        return $this->discounts()->where('customer_id', $userId)->value('discount') ?? 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
