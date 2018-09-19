<?php

namespace App\Http\Resources;

use App\Enums\RoleType;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="GoodsResource",
 *     @SWG\Property(property="id", type="integer", description="Goods id"),
 *     @SWG\Property(property="user_id", type="integer", description="User id"),
 *     @SWG\Property(property="category_id", type="string", description="Category id"),
 *     @SWG\Property(property="name", type="string", description="Goods name"),
 *     @SWG\Property(property="brand", type="string", description="Goods brand"),
 *     @SWG\Property(property="description", type="string", description="Goods description"),
 *     @SWG\Property(property="quantity_actual", type="integer", description="Actual number of goods"),
 *     @SWG\Property(property="quantity_available", type="integer", description="Available number of goods"),
 *     @SWG\Property(property="price", type="integer", description="Goods price 0.00"),
 *     @SWG\Property(property="article", type="string", description="Goods article"),
 *     @SWG\Property(property="country", type="string", description="Country of origin of the goods"),
 *     @SWG\Property(property="discount", type="integer", description="Percent"),
 * )
 */
class GoodsResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();

        return [
            'id'                 => $this->id,
            // 'user_id'          => $this->user_id,
            'provider'           => $this->whenLoaded('user', [
                'id'         => $this->user->id,
                'name'       => $this->user->name,
                'legal_name' => $this->user->legal_name,
                'rating'     => $this->user->rating(),
            ]),
            'category_id'        => $this->category_id,
            'article'            => $this->article,
            'name'               => $this->name,
            'brand'              => $this->brand,
            'description'        => $this->description,
            'quantity_actual'    => $this->quantity_actual,
            'quantity_available' => $this->quantity_available,
            'price'              => $this->price,

            $this->mergeWhen($user->hasRole(RoleType::CUSTOMER), [
                'discount' => $this->discount($user->id),
            ]),

            'country' => $this->country,
            'rating'  => $this->rating(),
            'images'  => ImageResource::collection($this->images),
        ];
    }
}
