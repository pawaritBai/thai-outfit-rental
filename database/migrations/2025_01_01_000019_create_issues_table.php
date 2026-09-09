<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง issues — เรื่องร้องเรียน/แจ้งปัญหาจากผู้ใช้ ส่งถึง admin
 * (เขียนใหม่แทน _needs_rewrite/2025_03_22_030910_create_issues_table.php ที่ขาด file_path
 *  และใช้ integer แทน unsignedBigInteger กับ FK)
 *
 * ที่มาของข้อมูล:
 *   app/Models/Issue.php
 *     - $table = 'issues' (ตัวเล็ก), ไม่มี $primaryKey → ใช้ 'id'
 *     - $fillable = ['user_id','title','description','file_path','reply','status','created_at','updated_at']
 *     - user() : belongsTo(User, 'user_id', 'user_id')
 *     - notifications() : hasMany(Notifications, 'issue_id')
 *
 *   app/Http/Controllers/IssueController.php
 *     - reportPage()/workStore()/... : Issue::create([user_id, title, description,
 *       status='reported', file_path=$filePath|null])   ← ไม่ส่ง reply
 *     - reply() : $issue->reply = ...  (เซ็ตทีหลัง → reply ต้อง nullable)
 *     - validate: title max:256 | description max:1000 | file nullable
 *     - status: reported → in_progress → fixed
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();

            // FK → Users.user_id
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();

            $table->string('title', 256);
            $table->text('description');
            $table->string('file_path')->nullable();          // path รูปแนบ (null ถ้าไม่แนบ)
            $table->text('reply')->nullable();                // admin ตอบทีหลัง
            $table->string('status')->default('reported');    // reported / in_progress / fixed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
