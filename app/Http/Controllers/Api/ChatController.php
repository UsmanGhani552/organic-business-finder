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
use Illuminate\Support\Facades\Log;

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

            $conversations = Conversation::with('receiver', 'otherUser', 'lastMessage')
                ->where('sender_id', $authUserId)
                ->orderBy('last_message_id','desc')->get();
                // dd($conversations);

            $conversations->each(function ($conversation) use ($authUserId) {
                // dd($conversation['sender_id']);
                $otherUserId = $conversation['sender_id'] === $authUserId
                    ? $conversation['receiver_id']
                    : $conversation['sender_id'];

                $conversation->unread_count = Chat::where('receiver_id', $authUserId)
                    ->where('sender_id', $otherUserId)
                    ->where('is_read', 0) 
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
            Chat::sendMessage($request->validated());
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

    public function handleWebhook(Request $request)
    {
        try {
            // Log the raw payload for debugging
            Log::info('Webhook received:', [
                'headers' => $request->headers->all(),
                'payload' => $request->getContent()
            ]);

            // Optionally log or store the received data without checking the signature
            // Chat::create($request->all());

            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
                ->where('is_read', 1)
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
    public function totalUnreadCount()
    {
        try {
            // $user_id = intval($userId);
            $authUserId = Auth::id();
            // dd($authUserId);
            $count = Chat::where('receiver_id', $authUserId)
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
