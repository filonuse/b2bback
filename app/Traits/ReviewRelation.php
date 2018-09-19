<?php

namespace App\Traits;


trait ReviewRelation
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reviews()
    {
        return $this->morphMany('App\Models\Review', 'reviewable');
    }

    /**
     * Get a rating user in the application.
     *
     * @return int
     */
    public function rating()
    {
        $avg = $this->reviews()->avg('estimate');

        return $avg ? round($avg) : 0;
    }
}