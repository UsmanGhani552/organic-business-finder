<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function login(Request $request)
    {
        // $request->validate([
        //     'id_token' => 'required|string',
        // ]);

        // $idToken = $request->input('id_token');
        $idToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiY29tLm9yZ2FuaWNwcm9kdWNlLmNvIiwic3ViIjoidXNlcjEyMzQiLCJlbWFpbCI6InVzbWFuLmNlbnRvc3F1YXJlQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJleHAiOjE3MzUwMzcxODd9.IqZ0wqGRIhYPqdfg3RRyMotKlrfNklqkkDRbDQH1-RY";

        try {
            // $payload = [
            //     'iss' => 'https://appleid.apple.com',
            //     'aud' => 'com.organicproduce.co',
            //     'sub' => 'user1234',
            //     'email' => 'usman.centosquare@gmail.com',
            //     'email_verified' => true,
            //     'exp' => time() + 3600
            // ];

            // $jwt = JWT::encode($payload, env('FIREBASE_PRIVATE_KEY'), 'HS256');
            // dd($jwt);
            // Get Apple's public keys
            $appleKeysUrl = 'https://appleid.apple.com/auth/keys';
            $keys = file_get_contents($appleKeysUrl);
            $decodedKeys = json_decode($keys, true);

            // Decode the JWT
            $decodedToken = JWT::decode($idToken, new Key($decodedKeys['keys'][0]['n'], 'RS256'));
            dd($decodedToken);

            // Extract user information from the token
            $appleId = $decodedToken->sub; // User's unique identifier
            $email = $decodedToken->email ?? null;

            // Process user (register or login)
            $user = User::updateOrCreate(
                ['apple_id' => $appleId],
                ['email' => $email]
            );

            // Return a response
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid token',
                'details' => $e->getMessage(),
            ], 400);
        }
    }

    public function socialLogin(Request $request) {
        try {
            //code... 
            // dd($request->token);
            $userDetails = Socialite::driver('google')->userFromToken($request->token);
            dd($userDetails);
            $user = User::where('social_id', $request->social_id)->where('provider', $request->provider)->first();
            if (!$user) {
                // User doesn't exist, create a new user
                $user = User::create([
                    'name' => $userDetails->name,
                    'email' => $userDetails->email,
                    'password' => Hash ::make('user1234'),  
                    'social_id' => $userDetails->social_id,
                    'provider' => $userDetails->provider,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid token',
                'details' => $e->getMessage(),
            ], 400);
        }
    }
}
