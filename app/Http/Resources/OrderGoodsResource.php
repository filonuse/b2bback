<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * Class OrderGoodsResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     definition="OrderGoodsResource",
 *     type="object",
 *          @SWG\Property(property="id", type="integer", description="Goods Id"),
 *          @SWG\Property(property="category_id", type="integer"),
 *          @SWG\Property(property="article", type="string"),
 *          @SWG\Property(property="name", type="string"),
 *          @SWG\Property(property="brand", type="string"),
 *          @SWG\Property(property="quantity", type="integer"),
 *          @SWG\Property(property="quantity_available", type="integer"),
 *          @SWG\Property(property="price", type="number", format="double"),
 *          @SWG\Property(property="images", type="array",
 *              @SWG\Items(ref="#/definitions/ImageResource") ) )
 * )
 */
class OrderGoodsResource extends Resource
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
            'id'                 => $this->id,
            'category_id'        => $this->category_id,
            'article'            => $this->article,
            'name'               => $this->name,
            'brand'              => $this->brand,
            'quantity'           => $this->pivot->quantity,
            'quantity_available' => $this->quantity_available,
            'price'              => $this->when($this->pivot->price, $this->pivot->price, $this->price),

            $this->mergeWhen($request->routeIs('bags.index'), [
                'discount' => $this->discount($request->user()->id),
            ]),

            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
