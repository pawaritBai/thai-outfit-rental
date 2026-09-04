<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chatMessage';
    protected $primaryKey = 'message_id';
    protected $guarded = ['message_id'];
    protected $fillable = ['message', 'created_at', 'created_at', 'sender_id'];
}
