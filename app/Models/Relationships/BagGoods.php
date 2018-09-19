<?php

namespace App\Models\Relationships;

use Illuminate\Database\Eloquent\Model;

class BagGoods extends Model
{
    protected $fillable = ['bag_id', 'goods_id', 'quantity'];

    public $timestamps = false;
}
