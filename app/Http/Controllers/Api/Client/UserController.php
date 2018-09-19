<?php

namespace App\Http\Controllers\Api\Client;


use App\Enums\RoleType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;

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
     *     path="/client/users",
     *     tags={"Client: users"},
     *     summary="Display a listing of the providers|clients.",
     *     operationId="getClientUsers",
     *     @SWG\Parameter(name="filter[like]", in="query", type="string", description="Search by name users"),
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
        $auth     = \Auth::user();
        $per_page = (int)$request->get('per_page', 15);

        $users = $this->repository->filter($auth, $request->input('filter.like'))->paginate($per_page);

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
     *     path="/client/users/{id}",
     *     tags={"Client: users"},
     *     summary="Display the specified user.",
     *     operationId="showClientUser",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true),
     *     @SWG\Response( response="201", description="Success", @SWG\Schema(ref="#/definitions/UserResource")),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function show(Request $request, $id)
    {
        $auth = \Auth::user();
        $user = $this->repository->findOrFail($id);
        $userRole = $user->roleName();

        // Forbidden
        if ($userRole === RoleType::ADMIN) {
            return $this->sendForbiddenResponse();
        }
        // If the specified user is customer, the display the discount for him
        if ($auth->hasRole(RoleType::PROVIDER) && $userRole === RoleType::CUSTOMER) {
            $request->request->add(['provider' => $auth->id]);
        }

        $request->request->add(['reviews_count' => $user->reviews()->count()]);

        return UserResource::make($user);
    }

    /**
     * @SWG\Put(
     *     path="/client/users/{id}",
     *     tags={"Client: users"},
     *     summary="Update the specified resource in storage.",
     *     operationId="banClientUser",
     *     @SWG\Parameter(name="id", in="path", type="integer", required=true, description="User's Id"),
     *     @SWG\Parameter(name="blacklist", in="formData", type="boolean", required=true, enum={true, false}),
     *     @SWG\Parameter(name="discount", in="formData", type="integer", required=true),
     *     @SWG\Response( response="200", description="The user is banned/unbanned"),
     *     @SWG\Response( response="403", description="Forbidden"),
     *     @SWG\Response( response="500", description="Internal server error"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function update(Request $request, $id)
    {
        $user     = \Auth::user();
        $banned   = filter_var($request->blacklist, FILTER_VALIDATE_BOOLEAN);

        $this->toggleBlacklist($user, $id, $banned);

        if ($request->has('discount')) {
            $this->syncDiscount($user, $id, (int)$request->get('discount'));
        }

        return $this->sendSuccessResponse('Updated Successfully');
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

    /*
     | -------------------------------------------------------------------------
     |      Manipulation methods
     | -------------------------------------------------------------------------
     */

    /**
     * Add/Removed the specified user in the Blacklist.
     *
     * @param User $user
     * @param $id
     * @param $banned
     */
    protected function toggleBlacklist(User $user, $id, $banned)
    {
        if ($banned) {
            $user->blacklist()->attach($id);
        } else {
            $user->blacklist()->detach($id);
        }
    }

    /**
     * Set a discount for the specified user
     *
     * @param User $user
     * @param $id
     * @param int $value
     */
    protected function syncDiscount(User $user, $id, int $value)
    {
        if ($value > 0) {
            $user->discounts()->syncWithoutDetaching([$id => ['discount' => $value]]);;
        } else {
            $user->discounts()->detach($id);
        }
    }
}
