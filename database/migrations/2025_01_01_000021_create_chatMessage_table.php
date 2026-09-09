<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง chatMessage — จาก app/Models/ChatMessage.php
 *   $table = 'chatMessage', $primaryKey = 'message_id'
 *   $fillable = ['message','created_at','created_at','sender_id']  ('created_at' ซ้ำ = typo ในโค้ดเดิม)
 *   timestamps เปิด (default)
 *
 * ⚠️ dead code: ไม่มี controller/route/view ไหนใช้ ChatMessage เลย (ฟีเจอร์แชทค้าง)
 *   สร้าง table ให้ตรง model ไว้เผื่อต่อยอด — sender_id nullable เพราะไม่มีจุดสร้าง record
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatMessage', function (Blueprint $table) {
            $table->id('message_id');

            $table->unsignedBigInteger('sender_id')->nullable();
            $table->foreign('sender_id')
                  ->references('user_id')->on('Users')
                  ->nullOnDelete();


            $table->text('message');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatMessage');
    }
};
