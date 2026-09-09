<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง reportChats — จาก app/Models/ReportChat.php
 *   $table = 'reportChats', $primaryKey = 'chat_id'
 *   $fillable = ['created_at','reporter_id','recipient_id']
 *
 * dead code: ไม่มี controller/route/view ไหนใช้ ReportChat เลย
 *   reporter_id / recipient_id -> FK ไป Users.user_id ทั้งคู่ (nullable เพราะไม่มีจุดสร้าง record)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportChats', function (Blueprint $table) {
            $table->id('chat_id');

            $table->unsignedBigInteger('reporter_id')->nullable();
            $table->foreign('reporter_id')
                  ->references('user_id')->on('Users')
                  ->nullOnDelete();

            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->foreign('recipient_id')
                  ->references('user_id')->on('Users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportChats');
    }
};
