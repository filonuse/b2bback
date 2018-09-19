<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @SWG\Definition(
 *     type="object",
 *     definition="CategoryResource",
 *     @SWG\Property(property="id", type="integer", description="Category Id"),
 *     @SWG\Property(property="name", type="string", description="Category Name")
 * )
 */
class CategoryResource extends Resource
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
            'name'      => $this->name,
        ];
    }
}
