<?php

namespace App\Http\Controllers\Api\Client;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreRoute;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use App\Repositories\RouteRepository;
use App\Services\GoogleService;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class RouteController extends ApiController
{
    /**
     * RouteController constructor.
     * @param RouteRepository $repository
     */
    public function __construct(RouteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/client/routes",
     *     tags={"Client: routes"},
     *     summary="Display a listing of the routes.",
     *     operationId="clientIndexRoute",
     *     @SWG\Parameter(name="page", in="query", type="number", default="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="number", default="3"),
     *     @SWG\Response( response="200", description="Success", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/RouteResource"))),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $routes = $this->repository
            ->filter(\Auth::user())
            ->paginate((int)$request->get('per_page', 3));

        return RouteResource::collection($routes);
    }

    /**
     * @SWG\Post(
     *     path="/client/routes",
     *     tags={"Client: routes"},
     *     summary="Store a newly created route in storage.",
     *     operationId="clientStoreRoute",
     *     @SWG\Parameter(name="body", in="body", required=true,
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="time_start", type="string", description="H:i (tz:UTC)"),
     *              @SWG\Property(property="time_finish", type="string", description="H:i (tz:UTC)"),
     *              @SWG\Property(property="max_deviation", type="integer", description="Meters"),
     *              @SWG\Property(property="polyline_points", type="string"),
     *              @SWG\Property(property="addresses", type="array", @SWG\Items(type="object",
     *                  @SWG\Property(property="address", type="string"),
     *                  @SWG\Property(property="lat", type="number", format="float"),
     *                  @SWG\Property(property="lng", type="number", format="float"),
     *                  @SWG\Property(property="place_id", type="number", format="float"))),
     *              @SWG\Property(property="deviations", type="array", @SWG\Items(type="object",
     *                  @SWG\Property(property="distance", type="integer"),
     *                  @SWG\Property(property="price", type="number", format="float"),
     *                  @SWG\Property(property="percent", type="number", format="float"))) )),
     *     @SWG\Response(response="201", description="Success", @SWG\Schema(ref="#/definitions/RouteResource")),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function store(StoreRoute $request)
    {
        $this->authorize('create', Route::class);

        $directions = GoogleService::polylineDecode($request->get('polyline_points'));

        $request->merge([
            'user_id'    => \Auth::id(),
            'directions' => json_encode($directions),
        ]);

        $route = $this->repository->createWithRelations($request->all());

        $route->load(['addresses', 'deviations']);

        return RouteResource::make($route);
    }

    /**
     * @SWG\Get(
     *     path="/client/routes/{id}",
     *     tags={"Client: routes"},
     *     summary="Display the specified routes.",
     *     operationId="clientShowRoute",
     *     @SWG\Parameter(name="id", in="path", type="integer"),
     *     @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/RouteResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function show($id)
    {
        $route = $this->repository->findOrFail($id);

        $this->authorize('view', $route);

        $route->load(['addresses', 'deviations']);

        return RouteResource::make($route);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    /**
     * @SWG\Put(
     *     path="/client/routes/{id}",
     *     tags={"Client: routes"},
     *     summary="Update the specified route in storage.",
     *     operationId="clientUpdateRoute",
     *     @SWG\Parameter(name="id", in="path", type="integer"),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="time_start", type="string", description="H:i (tz:UTC)"),
     *              @SWG\Property(property="time_finish", type="string", description="H:i (tz:UTC)"),
     *              @SWG\Property(property="max_deviation", type="integer", description="Meters"),
     *              @SWG\Property(property="polyline_points", type="string"),
     *              @SWG\Property(property="addresses", type="array", @SWG\Items(type="object",
     *                  @SWG\Property(property="address", type="string"),
     *                  @SWG\Property(property="lat", type="number", format="float"),
     *                  @SWG\Property(property="lng", type="number", format="float"),
     *                  @SWG\Property(property="place_id", type="number", format="float"))),
     *              @SWG\Property(property="deviations", type="array", @SWG\Items(type="object",
     *                  @SWG\Property(property="distance", type="integer"),
     *                  @SWG\Property(property="price", type="number", format="float"),
     *                  @SWG\Property(property="percent", type="number", format="float"))) )),
     *     @SWG\Response(response="200", description="Success", @SWG\Schema(ref="#/definitions/RouteResource")),
     *     @SWG\Response(response="403", description="Forbidden."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function update(StoreRoute $request, $id)
    {
        $route = $this->repository->findOrFail($id);

        $this->authorize('update', $route);

        if ($points = $request->get('polyline_points', null)) {
            $request->merge([
                'directions' => json_encode(GoogleService::polylineDecode($points))
            ]);
        }

        $this->repository->updateWithRelations($route, $request->all());

        $route->load(['addresses', 'deviations']);

        return RouteResource::make($route);
    }

    /**
     * @SWG\Put(
     *     path="/client/routes/{id}/{activated}",
     *     tags={"Client: routes"},
     *     summary="Activate or deactivate a route",
     *     operationId="toggleActivatedRoute",
     *     @SWG\Parameter(name="id", in="path", type="integer"),
     *     @SWG\Parameter(name="activated", in="path", type="string", enum={"true", "false"}),
     *     @SWG\Response(response="200", description="Success"),
     *     @SWG\Response(response="404", description="Not found."),
     *     @SWG\Response(response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     *
     * @throws \Exception
     */
    public function toggleActivated($id, $activated)
    {
        $route = $this->repository->findOrFail($id);

        $this->authorize('update', $route);

        $route->update(['activated' => filter_var($activated, FILTER_VALIDATE_BOOLEAN)]);

        return $this->sendSuccessResponse('Success.');
    }

    /**
     * @SWG\Delete(
     *     path="/client/routes/{id}",
     *     tags={"Client: routes"},
     *     summary="Remove the specified route from storage.",
     *     operationId="clientDestroyRoute",
     *     @SWG\Parameter(name="id", in="path", type="integer"),
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
        $route = $this->repository->findOrFail($id);

        $this->authorize('delete', $route);

        $route->delete();

        return $this->sendSuccessResponse('Route deleted successfully.');
    }
}
