<?php

namespace App\Services;


use App\Repositories\ImageRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * @var string
     */
    private $disk;

    /**
     * @var ImageRepository
     */
    private $repository;

    /**
     * ImageService constructor.
     * @param string $disk
     * @param string $directory
     */
    public function __construct(string $disk = 'public')
    {
        $this->disk       = $disk;
        $this->repository = app(ImageRepository::class);
    }

    /**
     * @param string $disk
     */
    public function setDisk(string $disk)
    {
        $this->disk = $disk;
    }

    /**
     * Store a newly created image in storage and write the contents of a file.
     *
     * @param Model $model
     * @param array $images
     *
     * @return void
     */
    public function store(Model $model, array $images)
    {
        $className = get_class($model);
        $directory = app_class_base_name($className) . DIRECTORY_SEPARATOR . $model->id;

        if (!file_exists(public_path('storage' . DIRECTORY_SEPARATOR . $directory)))
            mkdir(public_path('storage' . DIRECTORY_SEPARATOR . $directory), 0777, true);

        foreach ($images as $image) {

            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $path             = $image->store($directory, $this->disk);
                $temp['filename'] = pathinfo($path, PATHINFO_BASENAME);
            } else {
                $temp = $this->parseBase64($image);
                Storage::disk($this->disk)->put($directory . DIRECTORY_SEPARATOR . $temp['filename'], $temp['content']);
            }

            $this->repository->create([
                'filename'        => $temp['filename'],
                'imagetable_id'   => $model->id,
                'imagetable_type' => $className,
            ]);
        }
    }

    /**
     * Delete the file at a given path and the specified resource from storage.
     *
     * @param array $ids The specified resource from storage
     * @return void
     * @throws \Exception
     */
    public function delete(array $ids)
    {
        $images = $this->repository->query()->whereIn('id', $ids)->get();

        foreach ($images as $image) {
            Storage::disk($this->disk)
                ->delete(app_class_base_name($image->imagetable_type) . DIRECTORY_SEPARATOR . $image->id . DIRECTORY_SEPARATOR . $image->filename);

            $image->delete();
        }
    }

    /**
     * @param $data
     * @return array
     */
    protected function parseBase64($data)
    {
        list($info, $content) = explode(',', $data);

        $mime     = explode('/', substr($info, 0, strpos($info, ';')));
        $filename = str_random(40) . '.' . $mime[1];

        return [
            'filename' => $filename,
            'content'  => base64_decode($content),
        ];
    }

    /**
     * Generate a base url for the image.
     *
     * @param  string $className
     * @param  string $path
     * @return string
     */
    public static function baseUrl($className, $path = '')
    {
        return url('storage/' . app_class_base_name($className) . '/' . $path);
    }
}