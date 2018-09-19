<?php

namespace App\Models;

use App\Events\Order\OrderCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'provider_id', 'current_status_id', 'store_id', 'amount_shipping', 'amount', 'paid'
    ];

    protected $dates = ['shipped_at', 'deleted_at'];

    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function statuses()
    {
        return $this->belongsToMany('App\Models\Status', 'order_statuses')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne('App\Models\Status', 'id', 'current_status_id');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasStatus(string $name)
    {
        return $this->status()->value('name') == $name;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this
            ->belongsToMany('App\Models\Goods', 'order_goods', 'order_id', 'goods_id')
            ->withPivot('quantity', 'price')
            ->withTrashed()
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne('App\Models\User', 'id','provider_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne('App\Models\User', 'id','customer_id');
    }
}
