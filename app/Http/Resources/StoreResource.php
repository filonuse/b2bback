<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * Class StoreResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="StoreResource",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="name", type="string"),
 *     @SWG\Property(property="legal_data", type="string"),
 *     @SWG\Property(property="address", ref="#/definitions/AddressResource"),
 *     @SWG\Property(property="created_at", type="string", format="date-time"),
 * )
 */
class StoreResource extends Resource
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
            'id'         => $this->id,
            'name'       => $this->name,
            'legal_data' => $this->legal_data,
            'address'    => AddressResource::make($this->whenLoaded('address')),
            'created_at' => $this->created_at,
        ];
    }
}
