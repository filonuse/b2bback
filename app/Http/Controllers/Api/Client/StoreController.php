<?php

namespace App\Http\Controllers\Api\Client;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CreateStore;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;

class StoreController extends ApiController
{
    /**
     * StoreController constructor.
     * @param StoreRepository $repository
     */
    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/stores",
     *     summary="Display a listing of the stores.",
     *     tags={"Client: stores"},
     *     operationId="indexClientStore",
     *     @SWG\Parameter(name="page", in="query", type="number", default="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="number", default="15"),
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/StoreResource"))
     *     ),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $stores = \Auth::user()
            ->stores()
            ->with('address')
            ->paginate((int)$request->get('per_page', 15));

        return StoreResource::collection($stores);
    }

    /**
     * @SWG\Post(
     *    path="/client/stores",
     *    tags={"Client: stores"},
     *    summary="Store a newly created store in storage.",
     *    operationId="storeClientStore",
     *    @SWG\Parameter( name="body", in="body", required=true,
     *          @SWG\Schema( type="object",
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="legal_data", type="string"),
     *              @SWG\Property(property="address", type="object",
     *                  @SWG\Property(property="address", type="string"),
     *                  @SWG\Property(property="lat", type="number"),
     *                  @SWG\Property(property="lng", type="number"),
     *                  @SWG\Property(property="place_id", type="string")))),
     *    @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/StoreResource")),
     *    @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *    @SWG\Response( response="403", description="Forbidden"),
     *    @SWG\Response( response="500", description="Internal server error"),
     *    security={{"Bearer": {}}}
     * )
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CreateStore $request)
    {
        $this->authorize('create', Store::class);

        $request->merge(['user_id' => \Auth::id()]);

        $store = $this->repository->createWithAddress($request->all())
            ->load('address');

        return StoreResource::make($store);
    }

    /**
     * @SWG\Get(
     *     path="/client/stores/{id}",
     *     tags={"Client: stores"},
     *     summary="Display the specified store.",
     *     operationId="showClientStore",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/StoreResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show($id)
    {
        $store = $this->repository->findOrFail($id);

        $store->load('address');

        return StoreResource::make($store);
    }

    /**
     * @SWG\Put(
     *    path="/client/stores/{id}",
     *    tags={"Client: stores"},
     *    summary="Update the specified store in storage.",
     *    operationId="updateClientStore",
     *    @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *    @SWG\Parameter( name="body", in="body", required=true,
     *          @SWG\Schema( type="object",
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="legal_data", type="string"),
     *              @SWG\Property(property="address", type="object",
     *                  @SWG\Property(property="address", type="string"),
     *                  @SWG\Property(property="lat", type="number"),
     *                  @SWG\Property(property="lng", type="number"),
     *                  @SWG\Property(property="place_id", type="string")))),
     *    @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/StoreResource")),
     *    @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *    @SWG\Response( response="403", description="Forbidden"),
     *    @SWG\Response( response="500", description="Internal server error"),
     *    security={{"Bearer": {}}}
     * )
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(CreateStore $request, $id)
    {
        $store = $this->repository->findOrFail($id);

        $this->authorize('update', $store);

        $this->repository->updateWithAddress($request->all(), $store);

        $store->load('address');

        return StoreResource::make($store);
    }

    /**
     * @SWG\Delete(
     *     path="/client/stores/{id}",
     *     tags={"Client: stores"},
     *     summary="Remove the specified store from storage.",
     *     operationId="deleteClientStore",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $store = $this->repository->findOrFail($id);

        $this->authorize('delete', $store);
        $store->delete();

        return $this->sendSuccessResponse('Store deleted successfully.');
    }
}
