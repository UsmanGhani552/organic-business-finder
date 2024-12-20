<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Services\FirebaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Exception\Messaging\NotFound;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        try {
            $user_id = auth()->user()->id;
            // dd($user_id);
            $notifications = Notification::where('user_id', $user_id)->get();
            return response()->json([
                'status_code' => 200,
                'notifications' => $notifications
            ]);
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
            // 'device_token' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // $deviceToken = $request->input('device_token');
        $deviceTokens = DeviceToken::all();
        $title = $request->input('title');
        $body = $request->input('body');
        $data = $request->input('data', []);

        // Resolve FirebaseService directly within the method
        $firebaseService = app(FirebaseService::class);
        try {
            $se = $firebaseService->sendNotificationToMultipleDevices($deviceTokens, $title, $body, $data);
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
}
