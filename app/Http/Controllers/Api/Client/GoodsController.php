<?php

namespace App\Http\Controllers\Api\Client;


use App\Enums\RoleType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreGoods;
use App\Http\Resources\GoodsResource;
use App\Models\Goods;
use App\Repositories\GoodsRepository;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class GoodsController extends ApiController
{
    const DEFAULT_FILTER = [
        'orderBy' => 'created_at,asc',
    ];

    /**
     * GoodsController constructor.
     * @param GoodsRepository $repository
     */
    public function __construct(GoodsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/goods",
     *     tags={"Client: goods"},
     *     summary="Display a listing of the goods.",
     *     operationId="getClientGoods",
     *     @SWG\Parameter(name="filters[owner]", type="number", in="query", description="Id of authorized user"),
     *     @SWG\Parameter(name="filters[like]", type="string", in="query", description="Search by article goods"),
     *     @SWG\Parameter(name="filters[category]", type="string", in="query", description="Example Value: 1,2,3"),
     *     @SWG\Parameter(name="filters[inRadiusDelivery]", type="string", in="query", description="Id of authorized user"),
     *     @SWG\Parameter(
     *          name="filters[orderBy]",
     *          in="query", type="string",
     *          enum={"name,asc", "name,desc", "price,asc", "price,desc", "created_at,asc", "created_at,desc"},
     *          default="created_at,asc",
     *          description="Example Value: column,direction"
     *     ),
     *     @SWG\Parameter(name="page", in="query", type="number", default="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="number", default="15"),
     *
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/GoodsResource"))
     *     ),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $user     = \Auth::user();
        $filters  = $request->get('filters', self::DEFAULT_FILTER);
        $per_page = $request->get('per_page', 15);

        $goods = $this->repository
            ->filter($user->id, $filters)
            ->paginate((int)$per_page);

        return GoodsResource::collection($goods)->additional(['filters' => $filters]);
    }

    /**
     * @SWG\Post(
     *    path="/client/goods",
     *    tags={"Client: goods"},
     *    summary="Store a newly created goods in storage.",
     *    operationId="storeClientGoods",
     *    consumes={"multipart/form-data"},
     *    @SWG\Parameter(name="category_id", type="integer", in="formData", required=true),
     *    @SWG\Parameter(name="name", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="brand", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="description", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="quantity_actual", type="integer", in="formData", required=true),
     *    @SWG\Parameter(name="price", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="article", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="country", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="images[]", type="file", in="formData", description="Images to upload"),
     *
     *    @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/GoodsResource")),
     *    @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *    @SWG\Response( response="403", description="Forbidden"),
     *    @SWG\Response( response="500", description="Internal server error"),
     *    security={{"Bearer": {}}}
     * )
     */
    public function store(StoreGoods $request)
    {
        $user = \Auth::user();

        if ($user->cant('create', Goods::class)) {
            return $this->sendForbiddenResponse();
        }

        $goods = $this->repository->create($this->preparedData($user->id, $request));

        if ($images = $request->images) {
            $service = new ImageService();
            $service->store($goods, $images);
        }

        return GoodsResource::make($goods);
    }

    /**
     * Get prepared data for to create goods in storage.
     *
     * @param int $userId
     * @param Request $request
     * @param int $quantity_reserve
     * @return array
     */
    private function preparedData(int $userId, Request $request, $quantity_reserve = 0)
    {
        $data = $request->only('category_id', 'article', 'name', 'brand', 'description', 'quantity_actual', 'price', 'country');

        $data['user_id']            = $userId;
        $data['quantity_available'] = $data['quantity_actual'] - $quantity_reserve;

        return $data;
    }

    /**
     * @SWG\Get(
     *     path="/client/goods/{id}",
     *     tags={"Client: goods"},
     *     summary="Display the specified goods.",
     *     operationId="showClientGoods",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/GoodsResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show(Request $request, $id)
    {
        $user  = \Auth::user();
        $goods = $this->repository->findOrFail($id);

        // You are getting data of the provider
        if ($goods->user_id === $user->id)
            $goods->load('user');

        return GoodsResource::make($goods);
    }

    /**
     * @SWG\Post(
     *    path="/client/goods/{id}",
     *    tags={"Client: goods"},
     *    summary="Update the specified goods in storage.",
     *    operationId="updateClientGoods",
     *
     *    @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *    @SWG\Parameter(name="category_id", type="integer", in="formData", required=true),
     *    @SWG\Parameter(name="name", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="brand", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="description", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="quantity_actual", type="integer", in="formData", required=true),
     *    @SWG\Parameter(name="price", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="article", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="country", type="string", in="formData", required=true),
     *    @SWG\Parameter(name="images[]", type="file", in="formData", description="The images to upload"),
     *    @SWG\Parameter(name="to_delete_images", type="array", in="formData", @SWG\Items(type="integer")),
     *
     *    @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/GoodsResource")),
     *    @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *    @SWG\Response( response="403", description="Forbidden"),
     *    @SWG\Response( response="500", description="Internal server error"),
     *    security={{"Bearer": {}}}
     * )
     */
    public function update(StoreGoods $request, $id)
    {
        $user    = \Auth::user();
        $goods   = $this->repository->findOrFail($id);
        $service = new ImageService;

        if ($user->cant('update', $goods)) {
            return $this->sendForbiddenResponse();
        }

        $goods->update($this->preparedData($user->id, $request, $goods->quantity_reserve));

        // Create images
        if ($images = $request->images) {
            $service->store($goods, $images);
        }
        // Delete images
        if ($toDelete = $request->to_delete_images) {
            $service->delete($toDelete);
        }

        return GoodsResource::make($goods);
    }

    /**
     * @SWG\Delete(
     *     path="/client/goods/{id}",
     *     tags={"Client: goods"},
     *     summary="Remove the specified goods from storage.",
     *     operationId="deleteGoods",     *
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),     *
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return $this->sendSuccessResponse('Goods deleted successfully.');
    }
}
