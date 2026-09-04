<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * สร้างจาก app/Models/User.php ($table='Users', $primaryKey='user_id')
 * + app/Http/Controllers/Auth/RegisteredUserController.php และ RegisterStaffController.php (validate rules)
 * + app/Http/Middleware/Is*.php (ค่า userType / status)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Users', function (Blueprint $table) {
            $table->id('user_id');

            $table->string('name');
            $table->string('email')->unique();          // validate: unique, email
            $table->string('phone', 20)->unique();      // validate: unique, max:20
            $table->string('username')->unique();       // validate: unique

            // ค่าที่เจอในโค้ด: customer, shop owner, photographer, make-up artist, admin
            $table->string('userType')->default('customer');
            $table->string('gender')->nullable();

            $table->string('profilePicture')->nullable();   // path รูปโปรไฟล์
            $table->string('identity_path')->nullable();     // path รูปบัตร ปชช. (สมัคร staff)

            // ค่าที่เจอในโค้ด: active, inactive  (isActive middleware เช็ค field นี้)
            $table->string('status')->default('active');
            $table->boolean('is_newUser')->default(true);

            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();  // $casts => datetime
            $table->rememberToken();                              // $hidden => remember_token
            $table->timestamps();                                // model ไม่ได้ตั้ง $timestamps=false
        });

        // ตารางของ Laravel เอง (ระบบ reset password) — โปรเจกต์ลบ migration default ทิ้งไป
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('Users');
    }
};
