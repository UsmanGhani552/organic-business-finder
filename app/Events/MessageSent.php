<?php
namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        // Broadcasting on a private channel named chat.{receiver_id}
        return new PrivateChannel('chat.' . $this->chat->receiver_id);
    }

    public function broadcastWith()
    {
        // Data sent to the client
        return [
            'message' => $this->chat->message,
            'sender_id' => $this->chat->sender_id,
            'receiver_id' => $this->chat->receiver_id,
            'created_at' => $this->chat->created_at->toDateTimeString(),
        ];
    }
}
