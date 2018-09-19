<?php

namespace App\Http\Controllers\Api;

use App\Services\GoogleService;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

class AddressController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/address/autocomplete",
     *     tags={"Addresses"},
     *     summary="Get place predictions",
     *     operationId="addressAutocomplete",
     *     @SWG\Parameter(name="input", in="query", type="string", required=true),
     *     @SWG\Parameter(name="lang", in="query", type="string", enum={"uk", "ru"}),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function autocomplete(Request $request)
    {
        $service = new GoogleService($request->get('lang', 'uk'));
        $data    = $service->autocomplete($request->get('input'));

        return $this->sendCustomResponse($data);
    }

    /**
     * @SWG\Get(
     *     path="/address/details",
     *     tags={"Addresses"},
     *     summary="Get more details about a point of interest",
     *     operationId="addressPlaceDetails",
     *     @SWG\Parameter(name="place_id", in="query", type="string", required=true),
     *     @SWG\Parameter(name="lang", in="query", type="string", enum={"uk", "ru"}),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function placeDetails(Request $request)
    {
        $service = new GoogleService($request->get('lang', 'uk'));
        $data    = $service->placeDetails($request->get('place_id'));

        return $this->sendCustomResponse($data);
    }
}
