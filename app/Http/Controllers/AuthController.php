<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService =$authService;
    }

    /**
     * Attempt to log in a user and generate a JWT token.
     *
     * This method checks the provided email and password. If they are correct,
     * a JWT token is returned. If not, an error message is returned.
     *
     * @param \Illuminate\Http\Request $request The login credentials (email and password).
     * @return \Illuminate\Http\JsonResponse The generated token or an error message.
     */
    public function login(Request $request)
    {
        $response = $this->authService->login($request);
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }

    /**
     * Register a new user and generate a JWT token.
     *
     * This method creates a new user with the provided name, email, and password,
     * then generates a JWT token for the new user.
     *
     * @param \Illuminate\Http\Request $request The registration data (name, email, password).
     * @return \Illuminate\Http\JsonResponse The created user and JWT token.
     */
    public function register(Request $request)
    {
        $response = $this->authService->register($request);
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }

    /**
     * Get the current authenticated user.
     *
     * This method returns the details of the currently authenticated user.
     * It fetches the user from the token passed in the request header.
     *
     * @return \Illuminate\Http\JsonResponse The authenticated user's information.
     */
    public function me()
    {
        $response = $this->authService->me();
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }

    public function logout()
    {
        $response = $this->authService->logout();
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }

    public function otpRequest(Request $request)
    {
        $response = $this->authService->otpRequest($request);
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }

    public function otpValidate(Request $request)
    {
        $response = $this->authService->otpValidate($request);
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }

    public function changePin(Request $request, $id)
    {
        $response = $this->authService->changePin($request, $id);
        if (isset($response['status']) && $response['status'] === true) {
            return ResponseHelper::success(
                $response['result'],
                $response['message'] ?? null
            );
        } else {
            return ResponseHelper::error(
                $response['result'],
                $response['message'] ?? null,
                $response['code']
            );
        }
    }
}
