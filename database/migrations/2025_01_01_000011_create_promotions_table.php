<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Promotions', function (Blueprint $table) {
            $table->id('promotion_id');

            // FK → Shops.shop_id
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                  ->references('shop_id')->on('Shops')
                  ->cascadeOnDelete();      

            $table->string('promotion_name');
            $table->string('description')->nullable();       // validate: nullable|string
            $table->string('promotion_code', 20)->unique();  // สุ่มจาก Str::random(8) แล้วใช้ค้นหา ต้อง unique
            $table->decimal('discount_amount', 10, 2);
            $table->date('start_date');                       // validate: date (ไม่มีเวลา), required
            $table->date('end_date');                         // required + after_or_equal:start_date (เช็คใน controller)
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Promotions');
    }
};
