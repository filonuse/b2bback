<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="ReminderResource",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="description", type="string"),
 *     @SWG\Property(property="date_at", type="string"),
 *     @SWG\Property(property="time_at", type="string"),
 *     @SWG\Property(property="activated", type="boolean")
 * )
 */
class ReminderResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'description' => $this->description,
            'date_at'     => $this->date_at,
            'on_days'     => $this->on_days ? json_decode($this->on_days) : null,
            'time_at'     => $this->time_at,
            'activated'   => $this->activated,
        ];
    }
}
