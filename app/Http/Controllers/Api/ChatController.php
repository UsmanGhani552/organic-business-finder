<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Models\Chat;
use App\Models\Conversation;
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
            });
            // dd($chats);
            // Update the chats
            $affectedRows = $chats->update([
                'is_read' => 1
            ]);
            $updatedChats = $chats->orderBy('created_at')->get();
            return response()->json([
                'status_code' => 200,
                'chats' => $updatedChats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getMyChats()
    {
        try {
            $authUserId = Auth::id();

            // Fetch conversations with the other user and their last message
            $conversations = Conversation::with('otherUser', 'lastMessage')
                ->where('sender_id', $authUserId)
                // ->orWhere('receiver_id', $authUserId)
                ->get();

            // Add unread count for each conversation
            $conversations->each(function ($conversation) use ($authUserId) {
                $otherUserId = $conversation->sender_id === $authUserId
                    ? $conversation->receiver_id
                    : $conversation->sender_id;

                $conversation->unread_count = Chat::where('receiver_id', $authUserId)
                    ->where('sender_id', $otherUserId)
                    ->where('is_read', 0) // Count only unread messages
                    ->count();
            });

            return response()->json([
                'status_code' => 200,
                'chats' => $conversations,
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
                'message' => 'Message Sent Successfully'
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
            // $user_id = intval($userId);
            $authUserId = Auth::id();
            // dd($authUserId);
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
