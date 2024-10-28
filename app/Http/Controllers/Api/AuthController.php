<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try {
            $user = User::registerUser($request->validated());
            $token = $user->createToken('Organic-Business-Finder')->accessToken;
            
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
                $user->saveFcmToken($request->fcm_token);
                $token = $user->createToken('Organic-Business-Finder')->accessToken;
    
                return response()->json([
                    'status_code' => 200,
                    'message' => 'User Login Successfully',
                    'user' => $user,
                    'token' => $token
                ]);
            } else {
                return response()->json([
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
}
