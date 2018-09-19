<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="NewsResource",
 *     @SWG\Property(property="id", type="integer", description="News Id"),
 *     @SWG\Property(property="description", type="string", description="News Description"),
 *     @SWG\Property(property="created_at", type="object", description="News date of created",
 *          @SWG\Property(property="date", type="string", description="2018-05-21 14:33:05.000000"),
 *          @SWG\Property(property="timezone_type", type="integer", description="3"),
 *          @SWG\Property(property="timezone", type="string", description="UTC")
 *     ),
 *     @SWG\Property(property="images", type="array", @SWG\Items(ref="#/definitions/ImageResource"))
 * )
 */
class NewsResource extends Resource
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at'  => $this->created_at,
            'images'      => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
