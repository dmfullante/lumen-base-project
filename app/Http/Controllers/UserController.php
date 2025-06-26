<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService =$userService;
    }

    public function listData(Request $request)
    {
        $response = $this->userService->listData($request);
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
