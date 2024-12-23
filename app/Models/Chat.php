<?php

namespace App\Models;

use App\Services\FirebaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id', 'message', 'is_read'];

    public static function sendMessage($data)
    {
        try {
            $user = User::where('id',$data['receiver_id'])->first();
            $sender = auth()->user();
            $deviceTokens = $user->deviceTokens;
            $conversationBoth = Conversation::where(function ($query) use ($data) {
                $query->where('sender_id', $data['sender_id'])
                    ->where('receiver_id', $data['receiver_id']);
            })->orWhere(function ($query) use ($data) {
                $query->where('sender_id', $data['receiver_id'])
                    ->where('receiver_id', $data['sender_id']);
            })->get();
            $conversation = $conversationBoth->where('sender_id', $data['sender_id'])->first();
            $conversation2 = $conversationBoth->where('sender_id', $data['receiver_id'])->first();
            $imageUrl = $sender->image;
            if (!$conversation) {
                $title = "A {$user->type} sends a message.";
                $body = "Hi, you have a new inquiry from {$user->name}. Tap to reply.";
                
                $conversation = Conversation::create([
                    'sender_id' => $data['sender_id'],
                    'receiver_id' => $data['receiver_id'],
                ]);
                $conversation2 = Conversation::create([
                    'sender_id' => $data['receiver_id'],
                    'receiver_id' => $data['sender_id'],
                ]);
            } else {
                $title = $user->name ?? explode('@',$user->email)[0] . " Sends a message";
                $body = $data['message'];
            }
            $firebaseService = app(FirebaseService::class);
            $res = $firebaseService->sendNotificationToMultipleDevices($deviceTokens, $title, $body,$imageUrl);
            // dd($res);
            $chat = Chat::create([
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'conversation_id' => $conversation->id,
                'message' => $data['message'],
                'created_at' => Carbon::now()
            ]);
            $conversation->update([
                'last_message_id' => $chat->id,
            ]);
            $conversation2->update([
                'last_message_id' => $chat->id,
            ]);
            return $chat;
        } catch (Exception $e) {
            dd($e);
        }
    }
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
    // Relationship with sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relationship with receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
