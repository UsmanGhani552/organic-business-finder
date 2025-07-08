<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Mail\SendOtpMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendOtp(SendOtpRequest $request)
    {
        try {
            DB::beginTransaction();
            $email = $request->validated()['email'];
            $user = User::where('email', $email)->first();

            $otp = rand(100000, 999999);
            User::saveOtp($user,$otp);
            DB::commit();
            Mail::to($user->email)->send(new SendOtpMail($otp));
            return response()->json([
                'status_code' => 200,
                'message' => 'OTP sent successfully',
                'otp' => $otp,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function verifyOtp(VerifyOtpRequest $request) {
        try {
            $email = $request->validated()['email'];
            $otp = $request->validated()['otp'];
            $user = User::where('email', $email)->first();
            if($user->otp_expires_at < now()){
                return response()->json([
                    'status_code' => 400,
                    'message' => 'OTP Expired',
                ], 400);
            }else if($otp !== $user->otp){
                return response()->json([
                    'status_code' => 400,
                    'message' => 'OTP Does Not Match',
                ], 400);
            }
            return response()->json([
                'status_code' => 200,
                'user_id' => $user->id,
                'message' => 'OTP verified successfully',
                // 'otp' => $otp,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function resetPassword(ResetPasswordRequest $request) {
        try {
            $user_id = $request->validated()['user_id'];
            $user = User::where('id',$user_id)->first();
            User::changePassword($user,$request->validated());
            return response()->json([
                'status_code' => 200,
                'message' => 'Password Reset Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
