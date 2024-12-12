<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id', 'message', 'is_read'];

    public static function sendMessage($data)
    {
        try {
            $conversationBoth = Conversation::where(function ($query) use ($data) {
                $query->where('sender_id', $data['sender_id'])
                    ->where('receiver_id', $data['receiver_id']);
            })->orWhere(function ($query) use ($data) {
                $query->where('sender_id', $data['receiver_id'])
                    ->where('receiver_id', $data['sender_id']);
            })->get()->toArray();
            $conversation = $conversationBoth->where('sender_id', $data['sender_id'])->first();
            if (!$conversation) {
                $conversation = Conversation::create([
                    'sender_id' => $data['sender_id'],
                    'receiver_id' => $data['receiver_id'],
                ]);
            }
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
            $conversation = $conversationBoth->where('sender_id', $data['receiver_id'])->first();
            if($conversation){
                $conversation->update([
                'last_message_id' => $chat->id,
            ]);
            }

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
