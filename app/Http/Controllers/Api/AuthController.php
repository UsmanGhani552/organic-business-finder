<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\FirebaseService;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Exception\Messaging\NotFound;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::registerUser($request->validated());
            $token = $user->createToken('Organic-Business-Finder')->accessToken;
            DB::commit();
            return response()->json([
                'status_code' => 200,
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {   
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                
                if ($user->type == 'farmer' && $user->subscription_status == 0 || $user->subscription_status == null) {
                    if ($user->is_free_trial && $user->free_trial_ends_at < now()) {
                        $user->endFreeTrial();
                    }
                }
                $user->saveFcmToken($request->fcm_token);
                $token = $user->createToken('Organic-Business-Finder')->accessToken;

                return response()->json([
                    'status_code' => 200,
                    'message' => 'User Login Successfully',
                    'user' => $user,
                    'token' => $token
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 400,
                    'error' => 'Invalid email or password'
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->token()->revoke();
            return response()->json([
                'status_code' => 200,
                'message' => 'User Logged Out Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getName() {}
}
