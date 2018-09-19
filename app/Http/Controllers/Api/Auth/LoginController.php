<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Swagger\Annotations as SWG;

use Illuminate\Http\Request;

class LoginController extends ApiController
{
    /**
     * @SWG\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     description="Handle a login request to the application.",
     *     operationId="login",
     *     @SWG\Parameter(in="formData", name="phone", type="string", required=true),
     *     @SWG\Parameter(in="formData", name="password", type="string", format="password", required=true),
     *
     *     @SWG\Response( response="200", description="Success."),
     *     @SWG\Response( response="400", description="Invalid Credentials."),
     *     @SWG\Response( response="403", description="The account deleted|banned"),
     *     @SWG\Response( response="422", description="The given data was invalid."),
     *     @SWG\Response( response="500", description="Internal server error"),
     * )
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::withTrashed()
            ->where($this->username(), $request->{$this->username()})
            ->first();

        if (!$user) {
            return $this->sendInvalidCredentialsResponse();
        } elseif ($user->deleted_at != null) {
            return $this->sendCustomResponse(['message' => 'The account deleted', 'errors' => UserStatus::DELETED], 403);
        } elseif (!$token = \JWTAuth::attempt($this->credentials($request))) {
            return $this->sendInvalidCredentialsResponse();
        }

        $user = \Auth::user();
        if ($user->banned) {
            \JWTAuth::invalidate($token);
            return $this->sendCustomResponse(['message' => 'The account banned', 'errors' => UserStatus::BANNED], 403);
        }

        return UserResource::make($user)->additional(['token' => $token]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password'        => 'required|string',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'phone';
    }

    public function sendBannedResponse()
    {
        return $this->sendCustomResponse('', 403);
    }

    /**
     * @SWG\Get(
     *     path="/logout",
     *     tags={"Auth"},
     *     description="Log the user out of the application.",
     *     operationId="logout",
     *     @SWG\Response( response="200", description="Success"),
     *     security={{"Bearer": {}}}
     * )
     */
    public function logout()
    {
        \JWTAuth::invalidate();

        return $this->sendSuccessResponse('Logged out Successfully.');
    }
}
