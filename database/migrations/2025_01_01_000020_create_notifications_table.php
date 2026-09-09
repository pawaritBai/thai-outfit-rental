<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง notifications — แจ้งเตือน (ปัจจุบันใช้แจ้ง admin เมื่อมี issue ใหม่)
 * (เขียนใหม่แทน _needs_rewrite/2025_03_22_031014_notifications.php ที่ใช้ constrained()
 *  ซึ่งเดาตารางเป็น users/id แทน Users/user_id)
 *
 * ที่มาของข้อมูล:
 *   app/Models/Notifications.php
 *     - $table = 'notifications', ไม่มี $primaryKey → 'id'
 *     - $fillable = ['user_id','issue_id','message','is_read','created_at','updated_at']
 *     - user() : belongsTo(User), issue() : belongsTo(Issue)
 *
 *   จุดสร้างเดียว: app/Http/Controllers/IssueController.php:84 (sendNotificationToAdmin)
 *     Notifications::create(['issue_id' => ..., 'message' => ...])
 *     → ไม่ส่ง user_id (nullable) และไม่ส่ง is_read (default false)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // FK → Users.user_id  (nullable — create() ไม่ส่งค่านี้)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('user_id')->on('Users')
                  ->nullOnDelete();

            // FK → issues.id
            $table->unsignedBigInteger('issue_id');
            $table->foreign('issue_id')
                  ->references('id')->on('issues')
                  ->cascadeOnDelete();

            $table->text('message');
            $table->boolean('is_read')->default(false);       // เพิ่งสร้าง = ยังไม่อ่าน

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
