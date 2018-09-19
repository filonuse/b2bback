<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="RouteResource",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="time_start", type="string"),
 *     @SWG\Property(property="time_finish", type="string"),
 *     @SWG\Property(property="max_deviation", type="integer"),
 *     @SWG\Property(property="processed", type="boolean"),
 *     @SWG\Property(property="activated", type="boolean"),
 *     @SWG\Property(property="directions", type="array",
 *          @SWG\Items(type="array",
 *                  @SWG\Items(type="number", format="float", description="lat, lng"))),
 *     @SWG\Property(property="addresses", type="array", @SWG\Items(ref="#/definitions/AddressResource")),
 *     @SWG\Property(property="deviations", type="array", @SWG\Items(type="object",
 *          @SWG\Property(property="id", type="integer"),
 *          @SWG\Property(property="distance", type="integer"),
 *          @SWG\Property(property="price", type="number", format="float"),
 *          @SWG\Property(property="percent", type="number", format="float")))
 * )
 */
class RouteResource extends Resource
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
            'id'            => $this->id,
            'time_start'    => $this->time_start,
            'time_finish'   => $this->time_finish,
            'max_deviation' => $this->max_deviation,
            'processed'     => $this->processed,
            'activated'     => $this->activated,
            'addresses'     => AddressResource::collection($this->whenLoaded('addresses')),
            'deviations'    => $this->whenLoaded('deviations'),
            'directions'    => json_decode($this->directions),
        ];
    }
}
