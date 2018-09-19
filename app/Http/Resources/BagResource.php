<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class BagResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     definition="BagResource",
 *     type="object",
 *     @SWG\Property(property="id", type="integer" ),
 *     @SWG\Property(property="provider", type="object",
 *         @SWG\Property(property="id", type="integer"),
 *         @SWG\Property(property="name", type="string"),
 *         @SWG\Property(property="legal_name", type="string"),
 *         @SWG\Property(property="rating", type="integer") ),
 *     @SWG\Property(property="goods", type="array", @SWG\Items(ref="#/definitions/OrderGoodsResource") ),
 *     @SWG\Property(property="images", type="array", @SWG\Items(ref="#/definitions/ImageResource") ) )
 * )
 */
class BagResource extends Resource
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
            'provider' => $this->whenLoaded('provider', [
                'id'         => $this->provider->id,
                'name'       => $this->provider->name,
                'legal_name' => $this->provider->legal_name,
                'rating'     => $this->provider->rating(),
            ]),
            'goods'    => OrderGoodsResource::collection($this->whenLoaded('goods')),
        ];
    }
}
