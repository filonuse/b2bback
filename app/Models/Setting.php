<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['user_id', 'name', 'value'];

    /**
     * @return bool
     */
    public function isUserNotify()
    {
        return strtolower($this->value) === 'on';
    }
}
