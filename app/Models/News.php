<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['name','description'];

    /**
     * Get all of the images.
     */
    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imagetable');
    }
}
