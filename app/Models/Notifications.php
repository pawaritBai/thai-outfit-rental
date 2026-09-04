<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = [
        'user_id', 
        'issue_id', 
        'message',  
        'is_read', 
        'created_at', 
        'updated_at', 
    ];

    // กำหนดว่า `user_id` จะถูกเชื่อมโยงกับ `id` ในตาราง `users`
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // กำหนดว่า `issue_id` จะถูกเชื่อมโยงกับ `id` ในตาราง `issues`
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}

