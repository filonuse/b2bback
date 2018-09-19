<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class LangController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/lang/{locale}",
     *     tags={"Localization"},
     *     description="Get language lines.",
     *     operationId="showLang",
     *     @SWG\Parameter(name="locale", in="path", type="string", required=true, enum={"ua", "ru"}),
     *     @SWG\Response(response="200", description="All language files return an object of keyed strings",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="welcome", type="string", example="Welcome to our application")
     *          )
     *     ),
     *     @SWG\Response(response="404", description="The requested resource was not found")
     * )
     */
    public function show($locale)
    {
        $data = \Lang::get('api', [], $locale);

        return is_array($data)  ? $this->sendCustomResponse($data)
                                : $this->sendNotFoundResponse();
    }
}
