<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * Class MessageResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="MessageResource",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="message", type="string"),
 *     @SWG\Property(property="from_user_id", type="integer"),
 *     @SWG\Property(property="to_user_id", type="integer"),
 *     @SWG\Property(property="read_at", type="string", format="date-time"),
 *     @SWG\Property(property="created_at", type="string", format="date-time"),
 * )
 */
class MessageResource extends Resource
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
            'id'      => $this->id,
            'message' => $this->message,

            $this->mergeWhen($this->relationLoaded('users'), [
                'from_user_id' => $this->users->from_user_id,
                'to_user_id'   => $this->users->to_user_id,
                'read_at'      => $this->users->read_at,
            ]),

            'created_at' => $this->created_at,
        ];
    }
}
