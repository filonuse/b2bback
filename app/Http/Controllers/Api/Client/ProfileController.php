<?php

namespace App\Http\Controllers\Api\Client;

use App\Enums\RoleType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdatePassword;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

use Swagger\Annotations as SWG;

class ProfileController extends ApiController
{
    /**
     * ProfileController constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendNotFoundResponse();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->sendNotFoundResponse();
    }

    /**
     * @SWG\Get(
     *     path="/client/profile/{id}",
     *     tags={"Client: profile"},
     *     summary="Display the specified user.",
     *     operationId="showProfile",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/UserResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show($id)
    {
        $user = $this->repository->findOrFail($id);

        // Forbidden
        if ($user->hasRole(RoleType::ADMIN)) {
            return $this->sendForbiddenResponse();
        }

        return UserResource::make($user);
    }

    /**
     * @SWG\Put(
     *     path="/client/profile/{id}",
     *     tags={"Client: profile"},
     *     summary="Update the specified user in storage.",
     *     operationId="updateProfile",
     *     @SWG\Parameter( name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter( name="body", in="body", required=true,
     *          @SWG\Schema( type="object",
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="legal_name", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="official_data", type="string"),
     *              @SWG\Property(property="requisites", type="string"),
     *              @SWG\Property(property="categories", type="array", @SWG\Items(type="integer", description="Category Id"))
     *          )
     *     ),
     *     @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/UserResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function update(StoreUser $request, $id)
    {
        if (\Gate::denies('profile-update', $id)) {
            return $this->sendForbiddenResponse();
        }

        $user = \Auth::user();
        $data = $request->only('name', 'legal_name', 'email', 'phone', 'official_data', 'requisites');

        if ($request->role == RoleType::PROVIDER) {
            $user->categories()->sync($request->categories);
        }

        $user->update($data);

        return UserResource::make($user);
    }

    /**
     * @SWG\Put(
     *     path="/client/profile/{id}/password",
     *     tags={"Client: profile"},
     *     summary="Update the specified user's password in storage.",
     *     operationId="updateProfilePassword",
     *     @SWG\Parameter( name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter( name="body", in="body", required=true,
     *          @SWG\Schema( type="object",
     *              @SWG\Property(property="password_current", type="string"),
     *              @SWG\Property(property="password_new", type="string"),
     *              @SWG\Property(property="password_new_confirmation", type="string")
     *          )
     *     ),
     *     @SWG\Response( response="200", description="Success"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *     @SWG\Response( response="400", description="Invalid Credentials."),
     *     security={{"Bearer": {}}}
     * )
     */
    public function updatePassword(UpdatePassword $request, $id)
    {
        if (\Gate::denies('profile-update', $id)) {
            return $this->sendForbiddenResponse();
        }

        $user = \Auth::user();

        if (password_verify($request->password_current, $user->password)) {
            $user->update(['password' => bcrypt($request->password_new)]);

            return $this->sendSuccessResponse('Password updated successfully');
        }

        return $this->sendInvalidCredentialsResponse(['password' => 'Current password do not match our records.']);
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
}
