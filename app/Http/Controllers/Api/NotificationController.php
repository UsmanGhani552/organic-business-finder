<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;

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
}
