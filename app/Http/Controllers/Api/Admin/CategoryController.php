<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreCategory;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;
use Swagger\Annotations as SWG;

use Illuminate\Http\Request;


/**
 * Class CategoryController
 *
 * @package App\Http\Controllers\Api\Admin
 */
class CategoryController extends ApiController
{
    public function __construct(CategoryRepository $category)
    {
        $this->repository = $category;
    }

    /**
     * @SWG\Get(
     *     path="/admin/categories",
     *     tags={"Admin: categories"},
     *     description="Display a listing of the categories.",
     *     operationId="getCategories",
     *     @SWG\Response( response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/CategoryResource"))
     *     ),
     *     @SWG\Response( response="403", description="Forbidden."),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $per_page = (int)$request->get('per_page', 15);

        return CategoryResource::collection($this->repository->paginate($per_page));
    }

    /**
     * @SWG\Post(
     *    path="/admin/categories",
     *    tags={"Admin: categories"},
     *    description="Store a newly created category in storage.",
     *    operationId="storeCategory",
     *    @SWG\Parameter(name="body", in="body", required=true,
     *      @SWG\Schema( type="object", @SWG\Property(property="name", type="string"))
     *    ),
     *    @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/CategoryResource")),
     *    @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *    @SWG\Response( response="403", description="Forbidden"),
     *    @SWG\Response( response="500", description="Internal server error"),
     *    security={{"Bearer": {}}}
     * )
     */
    public function store(StoreCategory $request)
    {
        $category = $this->repository->create($request->only('name'));

        return CategoryResource::make($category);
    }

    /**
     * @SWG\Get(
     *     path="/admin/categories/{id}",
     *     tags={"Admin: categories"},
     *     description="Display the specified categories.",
     *     operationId="showCategory",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/CategoryResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show($id)
    {
        $category = $this->repository->findOrFail($id);

        return CategoryResource::make($category);
    }

    /**
     * @SWG\Put(
     *     path="/admin/categories/{id}",
     *     tags={"Admin: categories"},
     *     description="Update the specified category in storage.",
     *     operationId="updateCategory",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *          @SWG\Schema( type="object", @SWG\Property(property="name", type="string"))
     *     ),
     *     @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/CategoryResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function update(StoreCategory $request, $id)
    {
        $category = $this->repository->findOrFail($id);
        $category->update($request->only('name'));

        return CategoryResource::make($category);
    }

    /**
     * @SWG\Delete(
     *     path="/admin/categories/{id}",
     *     tags={"Admin: categories"},
     *     description="Remove the specified category from storage.",
     *     operationId="deleteCategory",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return $this->sendSuccessResponse();
    }
}
