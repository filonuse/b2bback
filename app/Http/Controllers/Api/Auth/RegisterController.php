<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\StoreUser;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\ApiController;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\SettingService;
use Illuminate\Http\Request;

use Swagger\Annotations as SWG;

class RegisterController extends ApiController
{
    /**
     * RegisterController constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @SWG\Post(
     *     path="/registration",
     *     tags={"Auth"},
     *     description="Handle a registration request for the application.",
     *     operationId="register",
     *     @SWG\Parameter( name="body", in="body", required=true,
     *          @SWG\Schema( type="object",
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="legal_name", type="string"),
     *              @SWG\Property(property="password", type="string"),
     *              @SWG\Property(property="password_confirmation", type="string"),
     *              @SWG\Property(property="phone", type="string"),
     *              @SWG\Property(property="e-mail", type="string"),
     *              @SWG\Property(property="official_data", type="string"),
     *              @SWG\Property(property="requisites", type="string"),
     *              @SWG\Property(property="role", type="string", enum={"provider", "customer"}),
     *              @SWG\Property(property="categories", type="array", @SWG\Items(type="integer", description="Category Id"))
     *          )
     *     ),
     *     @SWG\Response( response="200", description="Success", @SWG\Schema(ref="#/definitions/UserResource")),
     *     @SWG\Response( response="422", description="The given data was invalid", @SWG\Schema(ref="#/definitions/ErrorModel")),
     *     @SWG\Response( response="500", description="Internal server error"),
     * )
     */
    public function register(StoreUser $request)
    {
        $user = $this->create($request->all());

        // Attach role
        $role = app()->make(RoleRepository::class)->findBy('name', $request->role);
        $user->roles()->attach($role->id);

        // Set default of settings
        SettingService::saveToDefault($user);

        // Attach categories
        $user->categories()->attach($request->categories);
        $user->load('categories');

        // Authenticate a user
        $token = \JWTAuth::attempt($request->only('phone', 'password'));

        return UserResource::make($user)->additional(['token' => $token]);
    }

    /**
     * Create a new user instance after a valid registration
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function create(array $data)
    {
        return $this->repository->create([
            'name'          => $data['name'],
            'legal_name'    => $data['legal_name'],
            'phone'         => $data['phone'],
            'email'         => $data['email'],
            'official_data' => $data['official_data'],
            'requisites'    => $data['requisites'],
            'password'      => bcrypt($data['password']),
        ]);
    }
}
