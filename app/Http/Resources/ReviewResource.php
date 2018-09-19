<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="ReviewResource",
 *     @SWG\Property(property="id", type="integer", description="Review Id"),
 *     @SWG\Property(property="user", type="object", description="Info about the user, which wrote a review",
 *          @SWG\Property(property="name", type="string"),
 *          @SWG\Property(property="avatar", type="string")
 *     ),
 *     @SWG\Property(property="review", type="string"),
 *     @SWG\Property(property="estimate", type="integer"),
 *     @SWG\Property(property="created_at", type="object", description="Review date of created",
 *          @SWG\Property(property="date", type="string", description="2018-05-21 14:33:05.000000"),
 *          @SWG\Property(property="timezone_type", type="integer", description="3"),
 *          @SWG\Property(property="timezone", type="string", description="UTC")
 *     )
 * )
 */
class ReviewResource extends Resource
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
            'id'        => $this->id,
            'user'      => [
                'name'   => $this->user->name,
                'avatar' => null,
            ],
            'review'    => $this->review,
            'estimate'  => $this->estimate,
            'create_at' => $this->created_at,
        ];
    }
}
