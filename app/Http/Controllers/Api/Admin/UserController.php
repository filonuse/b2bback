<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\StoreUser;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

use Swagger\Annotations as SWG;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api\Admin
 */
class UserController extends ApiController
{
    /**
     * UserController constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Get(
     *     path="/admin/users",
     *     tags={"Admin: users"},
     *     description="Display a listing of the users.",
     *     operationId="getUsers",
     *     @SWG\Parameter(name="role", in="query", type="string", enum={"provider", "customer"}),
     *     @SWG\Parameter(name="page", in="query", type="integer", description="1"),
     *     @SWG\Parameter(name="per_page", in="query", type="integer", description="15"),
     *     @SWG\Response(response="200", description="Success",
     *          @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/UserResource"))
     *     ),
     *     @SWG\Response( response="403", description="Forbidden."),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function index(Request $request)
    {
        $role     = $request->get('role', '');
        $per_page = $request->get('per_page', 15);

        $users = $this->repository->allUsers($role)->paginate((int)$per_page);

        return UserResource::collection($users);
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
     *     path="/admin/users/{id}",
     *     tags={"Admin: users"},
     *     description="Display the specified user.",
     *     operationId="showUser",
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

        return UserResource::make($user);
    }

    /**
     * @SWG\Put(
     *     path="/admin/users/{id}",
     *     tags={"Admin: users"},
     *     description="Update the specified user in storage.",
     *     operationId="updateUser",
     *     @SWG\Parameter( name="id", in="path", type="integer", required=true),
     *     @SWG\Parameter( name="body", in="body", required=true,
     *          @SWG\Schema( type="object",
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="legal_name", type="string"),
     *              @SWG\Property(property="password", type="string"),
     *              @SWG\Property(property="password_confirmation", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="official_data", type="string"),
     *              @SWG\Property(property="requisites", type="string"),
     *              @SWG\Property(property="role", type="string", enum={"provider", "customer"}),
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
        $user = $this->repository->findOrFail($id);
        $data = $request->only('name', 'legal_name', 'phone', 'official_data', 'requisites');

        if ($password = $request->get('password')) {
            $data['password'] = bcrypt($password);
        }

        $user->update($data);
        $user->categories()->sync($request->categories);

        return UserResource::make($user);
    }

    /**
     * @SWG\Put(
     *     path="/admin/user/{id}/{banned}",
     *     tags={"Admin: users"},
     *     description="Ban or Allow the specified user log in.",
     *     operationId="banUser",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true, description="User's Id"),
     *     @SWG\Parameter(name="banned", in="path", type="boolean", required=true, enum={true, false}),
     *     @SWG\Response( response="200", description="The user is banned/unbanned"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function ban($id, $banned)
    {
        $banned = filter_var($banned, FILTER_VALIDATE_BOOLEAN);

        $this->repository->update(['banned' => $banned], $id);

        return $this->sendSuccessResponse(sprintf('%s Successfully', ($banned ? 'Banned' : 'Unbanned')));
    }

    /**
     * @SWG\Delete(
     *     path="/admin/users/{id}",
     *     tags={"Admin: users"},
     *     description="Remove the specified user from storage.",
     *     operationId="deleteUser",
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
