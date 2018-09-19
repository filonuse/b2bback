<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['filename', 'imagetable_id', 'imagetable_type'];

    /**
     * Get all of the owning imagetable models.
     */
    public function imagetable()
    {
        return $this->morphTo();
    }
}
