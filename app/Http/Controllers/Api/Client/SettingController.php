<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\SettingResource;
use App\Repositories\SettingRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;


class SettingController extends ApiController
{
    /**
     * SettingController constructor.
     * @param SettingRepository $repository
     */
    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @SWG\Get(
     *     path="/client/settings",
     *     tags={"Client: settings"},
     *     summary="Display a listing of the settings.",
     *     operationId="indexClientSettings",
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array",
     *              @SWG\Items(type="object",
     *                  @SWG\Property(property="id", type="integer"),
     *                  @SWG\Property(property="name", type="string"),
     *                  @SWG\Property(property="value", type="string") ) ) ),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index()
    {
        $settings = \Auth::user()->settings()->get();

        return SettingResource::collection($settings);
    }

    /**
     * @SWG\Put(
     *     path="/client/settings/{id}",
     *     tags={"Client: settings"},
     *     summary="Update the specified setting in storage",
     *     operationId="updateClientSettings",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter(name="value", in="formData", type="string", enum={"on", "off"}, required=true),
     *     @SWG\Response(response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, ['value' => 'required|string']);

        $this->repository->update($request->only('value'), $id);

        return $this->sendSuccessResponse();
    }
}
