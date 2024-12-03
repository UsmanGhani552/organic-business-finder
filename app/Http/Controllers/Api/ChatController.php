<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Models\Chat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function fetchChats($userId)
    {
        try {
            $authUserId = Auth::id();

            $chats = Chat::where(function ($query) use ($authUserId, $userId) {
                $query->where('sender_id', $authUserId)
                    ->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($authUserId, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $authUserId);
            })->orderBy('created_at')->get();

            return response()->json([
                'status_code' => 200,
                'chats'=>$chats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Send a message
    public function sendMessage(SendMessageRequest $request)
    {
        try {     
            DB::beginTransaction();
            broadcast(new MessageSent($request->validated()))->toOthers();
            DB::commit();
            return response()->json([
                'status_code' => 200,
                // 'chat' => $chat
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Unread message count
    public function unreadCount($userId)
    {
        try {
            $authUserId = Auth::id();

            $count = Chat::where('receiver_id', $authUserId)
                ->where('sender_id', $userId)
                ->where('is_read', 0)
                ->count();

            return response()->json([
                'status_code' => 200,
                'unread_count' => $count
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
