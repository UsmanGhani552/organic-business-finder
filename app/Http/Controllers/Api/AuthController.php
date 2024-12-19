<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;
use App\Services\FirebaseService;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Exception\Messaging\NotFound;

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

    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $deviceToken = $request->input('device_token');
        $title = $request->input('title');
        $body = $request->input('body');
        $data = $request->input('data', []);

        // Resolve FirebaseService directly within the method
        $firebaseService = app(FirebaseService::class);
        try {
            $se = $firebaseService->sendNotification($deviceToken, $title, $body, $data);
            dd($se);
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (NotFound $e) {
            return response()->json([
                'message' => 'The device token is not recognized. It might have been unregistered or registered to a different Firebase project.',
                'error' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            // Handle other exceptions
            Log::error('Error sending Firebase notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while sending the notification.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showForm()
    {
        return view('firebase'); // Return the view with the notification form
    }

    public function saveToken(Request $request)
    {
        auth()->user()->update(['device_token'=>$request->token]);
        return response()->json(['token saved successfully.']);

        // Auth::user()->device_token =  $request->token;

        // Auth::user()->save();

        // return response()->json(['Token successfully stored.']);
    }
}
