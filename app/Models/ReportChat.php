<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportChat extends Model
{
    protected $table = 'reportChats';
    protected $primaryKey = 'chat_id';
    protected $guarded = ['chat_id'];
    protected $fillable = ['created_at', 'reporter_id', 'recipient_id'];
}
