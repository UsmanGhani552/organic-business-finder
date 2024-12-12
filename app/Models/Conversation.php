<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    public $sender_id;
    protected $table = 'conversations';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'last_message_id',
    ];

    public function lastMessage()
    {
        return $this->hasOne(Chat::class, 'id', 'last_message_id');
    }

    public function otherUser()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class,'receiver_id');
    }
}
