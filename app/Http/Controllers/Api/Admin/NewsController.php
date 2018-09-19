<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreNews;
use App\Http\Resources\NewsResource;
use App\Repositories\NewsRepository;
use App\Services\ImageService;
use Illuminate\Http\Request;

use Swagger\Annotations as SWG;


/**
 * Class NewsController
 * @package App\Http\Controllers\Api\Admin *
 */
class NewsController extends ApiController
{
    /**
     * NewsController constructor.
     * @param NewsRepository $repository
     */
    public function __construct(NewsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendNotFoundResponse();
    }

    /**
     * @SWG\Post(
     *    path="/admin/news",
     *    tags={"Admin: news"},
     *    description="Store a newly created news in storage",
     *    operationId="storeNews",
     *    @SWG\Parameter(name="description", in="formData", type="string"),
     *    @SWG\Parameter(name="images[]", type="file", in="formData", description="Images to upload"),
     *    @SWG\Response(response="201", description="Success", @SWG\Schema(ref="#/definitions/NewsResource")),
     *    @SWG\Response(response="403", description="Forbidden"),
     *    @SWG\Response(response="500", description="Internal server error"),
     *    security={{"Bearer": {}}}
     * )
     */
    public function store(StoreNews $request)
    {
        $news = $this->repository->create($request->only('name','description'));

        if ($images = $request->images) {
            $service = new ImageService();
            $service->store($news, $images);
        }

        $news->load('images');

        return NewsResource::make($news);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendNotFoundResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->sendNotFoundResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendNotFoundResponse();
    }
}
