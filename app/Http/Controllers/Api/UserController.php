<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\EditProfileRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function editProfile(EditProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $user = User::editProfile($user, $request->validated());
            return response()->json([
                'status_code' => 200,
                'message' => 'User Updated Successfully',
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Old password does not match',
                ], 400);
            }
            User::changePassword($user, $request->validated());
            return response()->json([
                'status_code' => 200,
                'message' => 'User Password Changed Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function editImage(Request $request){
        try {
            // dd($request->image);
            $validator = $request->validate([
                'image' => 'image'
            ]);
            // dd($validator);
            $user = Auth::user();
            User::editImage($user,$validator);
            return response()->json([
                'status_code' => 200,
                'message' => 'User Image Changed Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
