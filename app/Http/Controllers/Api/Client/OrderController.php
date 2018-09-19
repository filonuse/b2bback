<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreOrder;
use App\Http\Resources\BagResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\GoodsService;
use App\Services\RouteService;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class OrderController extends ApiController
{
    /**
     * OrderController constructor.
     * @param OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/orders",
     *     tags={"Client: orders"},
     *     summary="Display a listing of the orders.",
     *     operationId="indexClientOrder",
     *     @SWG\Parameter(name="filter[status]", in="query", type="integer", description="Status id"),
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/OrderBaseResource"))),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $orders = $this->repository
            ->filter(\Auth::user(), $request->input('filter.status'))
            ->paginate((int)$request->get('per_page', 15));

        return OrderResource::collection($orders);
    }

    /**
     * @SWG\Post(
     *     path="/client/orders",
     *     tags={"Client: orders"},
     *     summary="Store a newly created order in storage",
     *     operationId="storeClientOrder",
     *     @SWG\Parameter(name="body", in="body", required=true,
     *      @SWG\Schema(type="object",
     *          @SWG\Property(property="bag_id", type="integer"),
     *          @SWG\Property(property="amount", type="number", format="double"),
     *          @SWG\Property(property="store_id", type="integer"),
     *          @SWG\Property(property="goods", type="array",
     *              @SWG\Items(type="object",
     *                  @SWG\Property(property="id", type="integer"),
     *                  @SWG\Property(property="price", type="number", format="double"),
     *                  @SWG\Property(property="quantity", type="integer") ) ) ),
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(ref="#/definitions/OrderExtendsResource"))),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="409", description="Conflict"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function store(StoreOrder $request)
    {
        $this->authorize('create', Order::class);

        // The data is preparing
        $data                    = $request->only('amount', 'store_id');
        $data['goods']           = $this->getDataForSyncGoods($request->goods);
        $data['amount_shipping'] = 0;

        // Check goods
        $bag    = app('App\Repositories\BagRepository')->findOrFail($request->bag_id);
        $errors = [];

        foreach ($data['goods'] as $id => $goods) {
            if (!GoodsService::isAvailableQty($id, $goods['quantity'])) {
                array_push($errors, ['id' => $id]);
            }
        }

        if (!empty($errors)) {
            $bag->load('goods');

            return BagResource::make($bag)
                ->additional([
                    'errors' => [
                        'message' => 'Exceeded available quantity of goods',
                        'goods'   => $errors,
                    ],
                ])->response()->setStatusCode(409);
        }

        // Calculate the cost of delivery
        if ($data['store_id'] > 0) {
            $store                   = app('App\Repositories\StoreRepository')->findOrFail($data['store_id']);
            $data['amount_shipping'] = RouteService::calcAmountShipping($store, $bag->provider_id, $data['amount']);
        }

        $order = $this->repository->checkOut($bag, $data);

        return OrderResource::make($order);
    }

    /**
     * @SWG\Get(
     *     path="/client/orders/{id}",
     *     tags={"Client: orders"},
     *     summary="Display the specified order.",
     *     operationId="showClientOrder",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response(response="200", description="Success", ref="#/definitions/OrderExtendsResource"),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show($id)
    {
        $user  = \Auth::user();
        $order = $this->repository->findOrFail($id);

        if ($user->cant('view', $order)) {
            return $this->sendForbiddenResponse();
        }

        $order->load('goods.images');

        return OrderResource::make($order);
    }

    /**
     * @SWG\Put(
     *     path="/client/orders/{id}",
     *     tags={"Client: orders"},
     *     summary="Update the specified order in storage",
     *     operationId="updateClientOrder",
     *     @SWG\Parameter(name="id", in="path", required=true, type="integer"),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *      @SWG\Schema(type="object",
     *          @SWG\Property(property="amount", type="integer"),
     *          @SWG\Property(property="store_id", type="integer", description="Store id"),
     *          @SWG\Property(property="goods", type="array",
     *              @SWG\Items(type="object",
     *                  @SWG\Property(property="id", type="integer"),
     *                  @SWG\Property(property="price", type="number", format="double"),
     *                  @SWG\Property(property="quantity", type="integer") ) ) ) ),
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(ref="#/definitions/OrderGoodsResource")),
     *     @SWG\Response(response="403", description="Forbidden"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $order = $this->repository->findOrFail($id);

        $this->authorize('update', $order);

        $order->goods()->sync($this->getDataForSyncGoods($request->get('goods')));
        $order->update($request->only('amount', 'store_id'));
        $order->load('goods.images');

        return OrderResource::make($order);
    }

    /**
     * @SWG\Put(
     *     path="/client/orders/{id}/{status}",
     *     tags={"Client: orders"},
     *     summary="Change status the specified order in storage",
     *     operationId="changeClientStatusOrder",
     *     @SWG\Parameter(name="id", in="path", type="integer", description="Order id"),
     *     @SWG\Parameter(name="status", in="path", type="integer", description="Status id"),
     *     @SWG\Response(response="200", description="Success"),
     *     @SWG\Response(response="405", description="Method Not Allowed"),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function changeStatus($id, $status)
    {
        $order = $this->repository->findOrFail($id);

        if (\Auth::user()->cant('update', $order)) {
            return $this->sendCustomResponse(['message' => 'Method Not Allowed'], 405);
        }

        if ($order->current_status_id != $status) {
            $service = new StatusService($order);
            $service->save($status);

            return $this->sendSuccessResponse('Status changed Successfully.');
        }

        return $this->sendSuccessResponse('Status did not change.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendNotFoundResponse();
    }

    /*
     | -------------------------------------------------------------------------
     |      Manipulation methods
     | -------------------------------------------------------------------------
     */

    /**
     * Prepare data
     *
     * @param $goods
     * @return null|array
     */
    protected function getDataForSyncGoods(array $goods)
    {
        return array_reduce($goods, function ($carry, $item) {
            $carry[$item['id']] = [
                'quantity' => $item['quantity'],
                'price'    => $item['price'],
            ];

            return $carry;
        });
    }
}
