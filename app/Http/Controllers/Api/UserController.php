<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EditProfileRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function editProfile(EditProfileRequest $request){
        try {
            $user = Auth::user();
            $user = User::editProfile($user,$request->validated());
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
}
