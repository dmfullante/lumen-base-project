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

class AuthService extends BaseService
{
    public function login($request)
    {
        try {
            $rules = [
                'pin' => 'required|digits:6'
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::get()->firstWhere(function ($record) use ($request) {
                return Hash::check($request->pin, $record->pin);
            });

            if (!$user) {
                return [
                    'result' => [],
                    'message' => 'Invalid credentials',
                    'code' => 401,
                    'status' => false
                ];
            }

            $token = Auth::claims([
                '2fa_verified' => false
            ])->fromUser($user);
            return [
                'result' => [
                    'user' => $user,
                    'token' => $token
                ],
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
            Log::debug("Error in AuthService:login() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }

    public function register($request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'pin' => 'required|digits:6|unique:users'
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::create([
                'name' => $request->name,
                'pin' => Hash::make($request->pin),
                'email' => $request->email,
                'contact_no' => $request->contact_no
            ]);

            $token = Auth::fromUser($user);
            return [
                'result' => [
                    'user' => $user,
                    'token' => $token
                ],
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
            Log::debug("Error in AuthService:login() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }

    public function me()
    {
        try {
            $user = Auth::parseToken()->authenticate();
            return [
                'result' => compact('user'),
                'code' => 200,
                'status' => true,
                'message' => 'User successfully retrieved'
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
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return [
                'result' => [],
                'message' => 'User not authenticated',
                'code' => 401,
                'status' => false
            ];
        } catch (\Exception $err) {
            Log::debug("Error in AuthService:me() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }

    public function logout()
    {
        try {
            Auth::invalidate(Auth::getToken());
            return [
                'result' => [],
                'message' => 'User logged out successfully',
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
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return [
                'result' => [],
                'message' => 'Failed to log out',
                'code' => 500,
                'status' => false
            ];
        } catch (\Exception $err) {
            Log::debug("Error in AuthService:login() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }

    public function otpRequest($request)
    {
        try {
            $rules = [
                'email' => ['nullable', 'required_without:contact_no', 'exists:users,email'],
                'contact_no' => ['nullable', 'required_without:email', 'exists:users,contact_no'],
            ];
            
            $messages = [
                'email.required_without' => 'Email is required when contact number is not provided.',
                'email.exists' => 'Invalid email.',
                'contact_no.required_without' => 'Contact number is required when email is not provided.',
                'contact_no.exists' => 'Invalid contact number.'
            ];
    
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('email', $request->email)
                ->orWhere('contact_no', $request->contact_no)->first();

            if (!$user) {
                return [
                    'result' => [],
                    'message' => 'User not found',
                    'code' => 404,
                    'status' => false
                ];
            }

            // generate a unique OTP
            do {
                $otp = random_int(100000, 999999);
            } while (User::where('otp', $otp)->exists());
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);

            if ($user->save()) {
                $data = [
                    'subject' => 'OTP Code for Changing Vending Machine PIN',
                    'body' => "You're receiving this email because we received a 
                        request to verify your identity using a One-Time Password (OTP).",
                    'user' => $user,
                    'otp' => $user->otp,
                    'validity' => Carbon::parse($user->otp_expires_at)->format('Y-m-d h:i A'),
                    'template' => 'emails.otp'
                ];

                // Send notification
                $user->notify(new UserNotification($data));
            }

            return [
                'result' => [
                    'user' => $user
                ],
                'message' => 'An OTP has been sent to your email or contact number. 
                        Please follow the reset instructions to continue.',
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
            Log::debug("Error in AuthService:otpRequest() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }

    public function otpValidate($request)
    {
        try {
            $rules = [
                'otp' => 'required|digits:6'
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $user = User::where('otp', $request->otp)->first();

            if (!$user || Carbon::now()->greaterThanOrEqualTo($user->otp_expires_at)) {
                return [
                    'result' => [],
                    'message' => 'Invalid or expired OTP',
                    'code' => 401,
                    'status' => false
                ];
            }

            User::where('id', $user->id)->update(['otp_expires_at' => null]);

            $token = Auth::claims([
                '2fa_verified' => true
            ])->fromUser($user);

            return [
                'result' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'message' => 'OTP validated successfully.',
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
            Log::debug("Error in AuthService:otpValidate() in Line {$err->getLine()} :"
                . $err->getMessage());
            return [
                'result' => [],
                'message' => 'Ops, something went wrong.',
                'code' => 500,
                'status' => false
            ];
        }
    }

    public function changePin($request, $id)
    {
        try {
            $rules = [
                'new_pin' => 'required|digits:6',
                'confirm_pin' => 'required|same:new_pin'
            ];
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('id', $id)->first();
            if (!$user) {
                return [
                    'result' => [],
                    'message' => 'User not found',
                    'code' => 404,
                    'status' => false
                ];
            }

            if (Carbon::now()->greaterThanOrEqualTo($user->otp_expires_at)
            || $user->otp == null || $user->otp_expires_at == null) {
                return [
                    'result' => [],
                    'message' => 'Invalid or expired OTP',
                    'code' => 401,
                    'status' => false
                ];
            }

            $user->pin = Hash::make($request->new_pin);
            //reset OTP and its expiration
            $user->otp = null;
            $user->otp_expires_at = null;

            if ($user->save()) {
                Cache::forget("pin_reset_token_{$user->id}");
                $data = [
                    'subject' => 'Your Vending Machine PIN has been Changed',
                    'body' => "We would like to inform you that your
                        vending machine PIN has been successfully updated.",
                    'user' => $user,
                    'template' => 'emails.change_pin'
                ];

                // Send notification
                $user->notify(new UserNotification($data));
            }

            return [
                'result' => [
                    'user' => $user
                ],
                'message' => 'Your PIN has been changed successfully.',
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
            Log::debug("Error in AuthService:changePin() in Line {$err->getLine()} :"
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
