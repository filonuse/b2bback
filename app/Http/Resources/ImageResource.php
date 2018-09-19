<?php

namespace App\Http\Resources;


use App\Services\ImageService;
use Illuminate\Http\Resources\Json\Resource;
use Swagger\Annotations as SWG;

/**
 * Class ImageResource
 * @package App\Http\Resources
 *
 * @SWG\Definition(
 *     type="object",
 *     definition="ImageResource",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="url", type="string")
 * )
 */
class ImageResource extends Resource
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
            'id'  => $this->id,
            'url' => $this->getUrl(),
        ];
    }

    /**
     * @return string
     */
    private function getUrl()
    {
        $path = $this->imagetable_id . '/' . $this->filename;

        return ImageService::baseUrl($this->imagetable_type, $path);
    }
}
