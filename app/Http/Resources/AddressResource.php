<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class AddressResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="AddressResource",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="address", type="string"),
 *     @SWG\Property(property="lat", type="number", format="float"),
 *     @SWG\Property(property="lng", type="number", format="float"),
 *     @SWG\Property(property="place_id", type="string"),
 * )
 */
class AddressResource extends Resource
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
            'id'       => $this->id,
            'address'  => $this->address,
            'lat'      => $this->lat,
            'lng'      => $this->lng,
            'place_id' => $this->place_id,
        ];
    }
}
