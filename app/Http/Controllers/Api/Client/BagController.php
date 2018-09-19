<?php

namespace App\Http\Controllers\Api\Client;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreBag;
use App\Http\Resources\BagResource;
use App\Models\Bag;
use App\Repositories\BagRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class BagController extends ApiController
{
    /**
     * BagController constructor.
     * @param BagRepository $repository
     */
    public function __construct(BagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/bags",
     *     tags={"Client: bag"},
     *     summary="Display a listing of the goods in the bag.",
     *     operationId="indexClientBag",
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/BagResource"))),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index()
    {
        $bags = $this->repository->filter(\Auth::id())->get();

        return BagResource::collection($bags);
    }

    /**
     * @SWG\Response(response="goodsCount", description="Success",
     *          @SWG\Schema(type="object", @SWG\Property(property="count", type="integer")))),
     *
     * @SWG\Get(
     *     path="/client/bags/goods/count",
     *     tags={"Client: bag"},
     *     summary="Display a number of the goods in the bag.",
     *     operationId="goodsCountClientBag",
     *     @SWG\Response(response="200", ref="#/responses/goodsCount"),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function goodsCount()
    {
        $count = $this->repository->goodsCount(\Auth::id());

        return $this->sendCustomResponse(['count' => $count]);
    }

    /**
     * @SWG\Post(
     *     path="/client/bags",
     *     tags={"Client: bag"},
     *     summary="Store a newly created goods in bag",
     *     operationId="storeClientBag",
     *     @SWG\Parameter(name="provider_id", in="formData", type="integer", required=true),
     *     @SWG\Parameter(name="goods", in="formData", type="integer", required=true),
     *     @SWG\Parameter(name="quantity", in="formData", type="integer", required=true),
     *     @SWG\Response(response="201", ref="#/responses/goodsCount"),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function store(StoreBag $request)
    {
        $this->authorize('create', Bag::class);

        $user = \Auth::user();
        $bag  = $this->repository->query()->firstOrCreate([
            'customer_id' => $user->id,
            'provider_id' => $request->provider_id,
        ]);

        $bag->load(['goods' => function ($query) use ($request) {
            $query->where('goods_id', $request->goods);
        }]);

        if ($bag->goods->count()) {
            $goods    = $bag->goods->first();
            $quantity = ($goods->pivot->quantity + $request->quantity);

            $bag->goods()->updateExistingPivot($request->goods, ['quantity' => $quantity]);
        } else {
            $bag->goods()->attach([$request->goods => ['quantity' => $request->quantity]]);
        }

        $count = $this->repository->goodsCount($user->id);

        return $this->sendCustomResponse(['count_goods' => $count], 201);
    }

    /**
     * @SWG\Put(
     *     path="/client/bags/{id}",
     *     tags={"Client: bag"},
     *     summary="Update the specified goods in bag",
     *     operationId="updateClientBag",
     *     @SWG\Parameter(name="id", in="path", required=true, type="integer"),
     *     @SWG\Parameter(name="goods", in="formData", type="integer", required=true),
     *     @SWG\Parameter(name="quantity", in="formData", type="integer", required=true),
     *     @SWG\Response(response="200", description="Success"),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function update(StoreBag $request, $id)
    {
        $bag = $this->repository->findOrFail($id);

        $this->authorize('update', $bag);

        $bag->goods()->updateExistingPivot($request->goods, ['quantity' => $request->quantity]);

        return $this->sendSuccessResponse();
    }

    /**
     * @SWG\Delete(
     *     path="/client/bags/{id}",
     *     tags={"Client: bag"},
     *     summary="Remove the specified goods from the bag.",
     *     operationId="deleteClientBag",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter(name="goods_id", in="query", type="integer", required=true),
     *     @SWG\Response(response="200", description="Success"),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $bag = $this->repository->findOrFail($id);

        $bag->goods()->detach($request->goods_id);

        if (!$bag->goods()->count())
            $bag->delete();

        return $this->sendSuccessResponse();
    }
}
