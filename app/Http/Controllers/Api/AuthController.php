<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\AppleToken;
use App\Services\FirebaseService;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Laravel\Socialite\Facades\Socialite;

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

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:visitor,farmer',
            'provider' => 'required|in:google,apple',
            'token' => 'required',
            'fcm_token' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $user = null;
            if ($request->provider == 'google') {
                $user = $this->loginWithGoogle($request);
            } else {
                $this->loginWithApple($request);
            }
            return response()->json([
                'status_code' => 200,
                'message' => 'User Login Successfully',
                'user' => $user,
                'token' => $user->token
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function loginWithApple($request, $appleToken = null)
    {
        try {
            $token = $request->input('token');
            config()->set('services.apple.client_secret',$appleToken);
            $appleUser = Socialite::driver('apple')->userFromToken($token);
            $user = User::where('google_id', $appleUser->id)->first();
            if (!$user) {
                $user = User::updateOrCreate(['email' => $user->email], [
                    'name' => $user->name,
                    'apple_id' => $user->id,
                    'password' => encrypt('12345678')
                ]);
            }
            $user->saveFcmToken($request->fcm_token);
            $token = $user->createToken('Organic-Business-Finder')->accessToken;
            return response()->json([
                'status_code' => 200,
                'message' => 'User Login Successfully',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Throwable $th) {
            // Handle the exception
            return response()->json([
                'status_code' => 400,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

    private function loginWithGoogle($request)
    {
        try {
            $token = $request->input('token');
            $userData = json_decode(base64_decode(explode('.', $token)[1]), true);

            $user = User::where('social_id', $userData['sub'])->first();
            if (!$user) {
                $user = User::updateOrCreate(['email' => $userData['email']], [
                    'name' => $userData['name'],
                    'social_id' => $userData['sub'],
                    'provider' => 'google',
                    'type' => $request['type'],
                    'password' => Hash::make('12345678'),
                ]);
            }
            $user->saveFcmToken($request->fcm_token);
            $token = $user->createToken('Organic-Business-Finder')->accessToken;
            $user['token'] = $token;
            return $user;
        } catch (\Throwable $th) {
           throw $th;
        }
    }
}
