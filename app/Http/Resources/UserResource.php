<?php

namespace App\Http\Resources;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="UserResource",
 *     @SWG\Property(property="id", type="integer", description="User's Id"),
 *     @SWG\Property(property="legal_name", type="string", description="User's Legal Name"),
 *     @SWG\Property(property="email", type="string", description="User's email"),
 *     @SWG\Property(property="phone", type="string", description="User's Id"),
 *     @SWG\Property(property="official_data", type="string", description="User's official Data"),
 *     @SWG\Property(property="requisites", type="string", description="User's requisites"),
 *     @SWG\Property(property="rating", type="integer", description="A rating user in the application"),
 *     @SWG\Property(property="banned", type="string", format="boolean", description="Exists a user in the blacklist of the authorized user"),
 *     @SWG\Property(property="role", type="string", description="User's role"),
 *     @SWG\Property(property="categories", type="array", description="User's categories",
 *          @SWG\Items(ref="#/definitions/CategoryResource")
 *     ),
 * )
 */
class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        /**
         * @var User $authUser
         */
        $auth = $request->user();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'legal_name'    => $this->legal_name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'official_data' => $this->official_data,
            'requisites'    => $this->requisites,

            $this->mergeWhen($request->has('provider'), [
                'discount' => $this->discountFromProvider($request->provider),
            ]),
            $this->mergeWhen($request->has('reviews_count'), [
                'reviews_count' => $request->reviews_count,
            ]),

            'avatar'        => null,
            'rating'        => $this->rating(),
            'banned'        => $this->when($auth->hasRole(RoleType::ADMIN), $this->banned, $auth->checkBlacklist($this->id)),
            'deleted'       => ($this->deleted_at !== null),
            'role'          => $this->roles()->value('name'),
            'categories'    => CategoryResource::collection($this->categories),
        ];
    }
}
