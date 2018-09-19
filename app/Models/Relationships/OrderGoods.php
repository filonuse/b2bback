<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table = 'order_goods';

    protected $fillable = [
        'order_id', 'goods_id', 'quantity', 'price',
    ];
}
