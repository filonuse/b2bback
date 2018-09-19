<?php

namespace App\Http\Resources;


use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * Class NotificationResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     definition="NotificationResource",
 *     type="object",
 *          @SWG\Property(property="id", type="string"),
 *          @SWG\Property(property="from_user", type="object",
 *              @SWG\Property(property="id", type="integer"),
 *              @SWG\Property(property="name", type="string")),
 *          @SWG\Property(property="action_id", type="integer"),
 *          @SWG\Property(property="action", type="string"),
 *          @SWG\Property(property="value", type="string"),
 *          @SWG\Property(property="read", type="boolean"),
 *          @SWG\Property(property="created_at", type="string", format="date-time"),
 * )
 */
class NotificationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $fromUser = User::query()
            ->withTrashed()
            ->where('id', $this->data['from_user_id'])
            ->first();

        return [
            'id'         => $this->id,
            'from_user'  => [
                'id'   => $fromUser->id,
                'name' => $fromUser->name,
            ],
            'action_id'  => $this->data['action_id'],
            'action'     => $this->data['action'],
            'value'      => $this->data['value'],
            'read'       => ($this->read_at !== null),
            'created_at' => $this->created_at,
        ];
    }
}
