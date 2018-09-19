<?php

namespace App\Models\Relationships;

use App\Events\Order\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_statuses';

    protected $fillable = ['order_id', 'status_id'];

    protected $dispatchesEvents = [
        'saved' => OrderStatusUpdated::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('App\Models\Order', 'id','order_id');
    }
}
