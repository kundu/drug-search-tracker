<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\UserRegisterRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * Handles user registration by creating a new user record with the provided credentials.
     * On successful registration, it returns the user's information along with an access token.
     *
     * @param  UserRegisterRequest  $request  The validated request containing user credentials.
     * @return \Illuminate\Http\JsonResponse  A JSON response including the user's details and access token upon successful registration, or an error message in case of failure.
     * @throws \Exception If there is an internal server error during the registration process.
     */
    public function register(UserRegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->name, $request->email, $request->password);
            $token = $this->authService->getToken($user);
            return apiResponse(200, "User registered successfully", ["user" => $user, "access_token" =>$token, 'token_type' => 'Bearer']);
        } catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, "Internal server error");
        }
    }

    /**
     * Handle user login requests.
     *
     * Validates the incoming request for email and password. If the validation passes,
     * it attempts to log in the user using the provided credentials. Upon successful
     * authentication, it generates an access token and returns the user's data along with
     * the token. If the credentials are invalid, a 401 error is returned. Any other
     * unexpected errors are logged and result in a 500 error response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException If the validation fails.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        try {
            $user = $this->authService->login($request->email, $request->password);
            $token = $this->authService->getToken($user);
            return apiResponse(200, "User login successfully", ["user" => $user, "access_token" => $token, 'token_type' => 'Bearer']);
        } catch (InvalidCredentialsException $invalidCredentialsException) {
            return apiResponse(401, $invalidCredentialsException->getMessage());
        } catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, "Internal server error");
        }
    }

    /**
     * Log the user out by revoking the current token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return apiResponse(200, "Successfully logged out.");
        } catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, "Internal server error");
        }
    }
}
