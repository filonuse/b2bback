<?php

namespace App\Http\Controllers\Api;

use App\Traits\RestApi;
use App\Http\Controllers\Controller;
use Swagger\Annotations as SWG;

use Illuminate\Http\Request;

/**
 * Class ApiController
 * @package App\Http\Controllers\Api
 *
 * @SWG\Swagger(
 *      basePath="/api/v1.0",
 *      @SWG\Info(title="Vervechka API", version="1.0.0")
 * )
 *
 * @SWG\Tag(name="Auth")
 * @SWG\Tag(name="Admin: users")
 * @SWG\Tag(name="Admin: categories")
 * @SWG\Tag(name="Admin: news")
 *
 * @SWG\Definition(
 *      type="object",
 *      definition="ErrorModel",
 *      @SWG\Property(property="message", type="string", default="The given data was invalid."),
 *      @SWG\Property(property="errors", type="object", description="The contain all of the validation errors")
 * )
 */
class ApiController extends Controller
{
    use RestApi;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $repository;
}
