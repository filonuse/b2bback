<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Swagger\Annotations as SWG;

class CatalogController extends ApiController
{
    /**
     * @var \Czim\Repository\BaseRepository
     */
    protected $repository;

    /**
     * @var \Illuminate\Http\Resources\Json\Resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected  $criteria;

    /**
     * The list of the available catalogs
     *
     * @var array
     */
    protected $availableCatalogs = [
        'categories'     => [
            'repository' => '\\App\\Repositories\\CategoryRepository',
            'resource'   => '\\App\\Http\\Resources\\CategoryResource',
        ],
        'order_statuses' => [
            'repository' => '\\App\\Repositories\\StatusRepository',
            'resource'   => '\\App\\Http\\Resources\\StatusResource',
            'criteria'   => '\\App\\Repositories\\Criteria\\Status\\Order',
        ],
    ];

    /**
     * CatalogController constructor.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        $name = $request->route('catalog');

        if ($catalog = $this->getAvailableCatalog($name)) {
            $this->repository = app($catalog['repository']);
            $this->resource   = $catalog['resource'];

            if (key_exists('criteria', $catalog)) {
                $this->criteria = $catalog['criteria'];
            }
        } else {
            abort(404, 'The catalog not found!');
        }
    }

    /**
     * @SWG\Get(
     *     path="/catalogs/{catalog}",
     *     tags={"Catalogs"},
     *     summary="Display the resource of the specified catalog.",
     *     operationId="getCatalogs",
     *     @SWG\Parameter(name="catalog", in="path", type="string", required=true, enum={"categories", "order_statuses"}, description="Catalog name"),
     *     @SWG\Response( response="200", description="Success",
     *          @SWG\Schema(type="array",
     *              @SWG\Items(type="object",
     *                  @SWG\Property(property="id", type="integer"),
     *                  @SWG\Property(property="name", type="string"),
     *              )
     *          )
     *     ),
     *     @SWG\Response( response="500", description="Internal server error"),
     * )
     */
    public function index()
    {
        if ($this->criteria) {
            $this->repository->pushCriteria(new $this->criteria);
        }
        $catalog = $this->repository->all();

        return $this->resource::collection($catalog);
    }

    /**
     * @param string $catalog
     * @return array|null
     */
    private function getAvailableCatalog($catalog)
    {
        return key_exists($catalog, $this->availableCatalogs)
            ? $this->availableCatalogs[$catalog]
            : null;
    }
}
