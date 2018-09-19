<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * Class OrderResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="OrderBaseResource",
 *     @SWG\Property(property="id", type="integer", description="Order Id"),
 *     @SWG\Property(property="user", type="object",
 *          @SWG\Property(property="name", type="string"),
 *          @SWG\Property(property="legal_name", type="string")
 *     ),
 *     @SWG\Property(property="current_status_id", type="integer"),
 *     @SWG\Property(property="amount_shipping", type="number"),
 *     @SWG\Property(property="amount", type="number"),
 *     @SWG\Property(property="currency", type="string", default="UAH", description="Code currency"),
 *     @SWG\Property(property="shipped_at", type="string"),
 *     @SWG\Property(property="created_at", type="string"),
 * )
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="OrderExtendsResource",
 *     @SWG\Property(property="id", type="integer", description="Order Id"),
 *     @SWG\Property(property="current_status_id", type="integer"),
 *     @SWG\Property(property="amount_shipping", type="number"),
 *     @SWG\Property(property="amount", type="number"),
 *     @SWG\Property(property="currency", type="string", default="UAH", description="Code currency"),
 *     @SWG\Property(property="goods", type="array",
 *          @SWG\Items(ref="#/definitions/OrderGoodsResource")),
 * )
 */
class OrderResource extends Resource
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
            'id'                => $this->id,
            $this->mergeWhen($this->user_name, [
                'user' => [
                    'name'       => $this->user_name,
                    'legal_name' => $this->user_legal_name,
                ],
            ]),
            'current_status_id' => $this->current_status_id,
            'amount_shipping'   => $this->amount_shipping,
            'amount'            => $this->amount,
            'currency'          => $this->currency,
            'paid'              => $this->paid,
            'goods'             => OrderGoodsResource::collection($this->whenLoaded('goods')),
            'delivery'          => $this->store_id,
            'shipped_at'        => $this->shipped_at,
            'created_at'        => $this->created_at,
        ];
    }
}
