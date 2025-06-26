<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth as Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService extends BaseService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->model = $user;
        $this->user = $user;
    }

    public function listData($request)
    {
        try {
            $query = $this->listQuery($request);
            $users = $this->filterQueryString($request, $query);
            return [
                'result' => $users->get(),
                'message' => 'Successfully Login',
                'code' => 200,
                'status' => true
            ];
        } catch (ValidationException $err) {
            return [
                'result' => [
                    'errors' => $err->errors()
                ],
                'message' => 'Validation failed. Please check the input and try again.',
                'code' => 422,
                'status' => false
            ];
        } catch (\Exception $err) {
            Log::debug("Error in listToTable:listToTable() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }
}
