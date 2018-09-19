<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreReview;
use App\Http\Resources\ReviewResource;
use App\Repositories\ReviewRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class ReviewController extends ApiController
{
    /**
     * ReviewController constructor.
     * @param ReviewRepository $repository
     */
    public function __construct(ReviewRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/reviews",
     *     tags={"Client: reviews"},
     *     summary="Display a listing of the reviews",
     *     operationId="indexClientReview",
     *     @SWG\Parameter(name="able_type", in="query", type="string", required=true, enum={"user", "goods"}),
     *     @SWG\Parameter(name="able_id", in="query", type="integer", required=true, description="Type id"),
     *     @SWG\Response( response="200", description="Success", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ReviewResource"))),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $data['reviewable_id']   = $request->get('able_id');
        $data['reviewable_type'] = $this->getReviewableType($request->able_type);

        $reviews = $this->repository
            ->filter($data)
            ->paginate((int)$request->get('per_page', 15));

        return ReviewResource::collection($reviews);
    }

    /**
     * @SWG\Post(
     *     path="/client/reviews",
     *     tags={"Client: reviews"},
     *     summary="Store a newly created review in storage",
     *     operationId="storeClientReview",
     *     @SWG\Parameter(name="review", in="formData", type="string", required=true),
     *     @SWG\Parameter(name="estimate", in="formData", type="integer", required=true, enum={1, 2, 3, 4, 5}),
     *     @SWG\Parameter(name="able_type", in="formData", type="string", required=true, enum={"user", "goods"}),
     *     @SWG\Parameter(name="able_id", in="formData", type="integer", required=true, description="Type's id"),
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/ReviewResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function store(StoreReview $request)
    {
        if ($type = $this->getReviewableType($request->able_type)) {
            $review = $this->repository->create([
                'from_user_id'    => \Auth::id(),
                'review'          => $request->review,
                'estimate'        => $request->estimate,
                'reviewable_id'   => $request->able_id,
                'reviewable_type' => $type,
            ]);

            return ReviewResource::make($review);
        }

        return $this->sendInvalidDataResponse(['able_type' => $request->able_type . ' is not exists']);
    }

    /**
     * @SWG\Get(
     *     path="/client/reviews/{id}",
     *     tags={"Client: reviews"},
     *     summary="Display the specified review.",
     *     operationId="showClientReview",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/ReviewResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show($id)
    {
        $review = $this->repository->findOrFail($id);

        return ReviewResource::make($review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response|string
     */
    public function update(Request $request, $id)
    {
        $this->sendNotFoundResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $this->sendNotFoundResponse();
    }

    /*
     | -------------------------------------------------------------------------
     |      Manipulation methods
     | -------------------------------------------------------------------------
     */

    /**
     * @param string $name
     * @return null|string
     */
    protected function getReviewableType(string $name)
    {
        $modelName = 'App\\Models\\' . ucfirst($name);

        return class_exists($modelName) ? $modelName : null;
    }
}
