<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NewsResource;
use App\Repositories\NewsRepository;
use Czim\Repository\Criteria\Common\OrderBy;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use Carbon\Carbon;

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
     * @SWG\Get(
     *     path="/news",
     *     tags={"News"},
     *     summary="Display a listing of the news.",
     *     operationId="clientNewsIndex",
     *     @SWG\Parameter(name="page", in="query", type="number", default="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="number", default="15"),
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/NewsResource"))),
     *     @SWG\Response(response="500", description="Internal server error")
     * )
     */
    public function index(Request $request)
    {

        $news = $this->repository->latest()->where('created_at','>=',Carbon::now()->addHour(-24))->paginate((int)$request->get('per_page', 15));

        return NewsResource::collection($news);
    }

    /**
     * @SWG\Get(
     *     path="/news/{id}",
     *     tags={"News"},
     *     summary="Display the specified news.",
     *     operationId="showClientNew",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response(response="200", description="Success", ref="#/definitions/OrderExtendsResource"),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show($id)
    {
        $news = $this->repository->findOrFail($id);
        $news->load('images');

        return NewsResource::make($news);
    }
}
