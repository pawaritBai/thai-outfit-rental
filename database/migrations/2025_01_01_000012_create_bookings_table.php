<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง Bookings — คำสั่งจองชุด/บริการ 1 รายการ (1 booking = 1 ร้าน + 1 รอบจอง)
 *
 * ที่มาของข้อมูล:
 *   app/Models/Booking.php
 *     - protected $table = 'Bookings', $primaryKey = 'booking_id', $timestamps = false
 *     - $fillable = ['purchase_date','total_price','amount_staff','status','hasOverrented',
 *                    'created_at','shop_id','promotion_id','user_id','pickup_date','AddressID']
 *
 *   app/Http/Controllers/OrderController.php@store (บรรทัด 176-185)
 *   app/Http/Controllers/OrderDetailController.php (บรรทัด 182-191)
 *     สองจุดนี้คือที่เดียวที่สร้าง Booking จริง ใช้เทียบกันเพื่อรู้ว่า field ไหน nullable
 *
 * ⚠️ ข้อสังเกตสำคัญ: column "AddressID" ชื่อหลอก ไม่ได้ชี้ไปตาราง Address
 *   Booking.php มี 2 relationship ที่ใช้ column เดียวกันแต่อ้างคนละตาราง:
 *     address()         : belongsTo(Address::class, 'AddressID', 'AddressID')       ← ไม่มีใครเรียกใช้จริง (dead code)
 *     customerAddress() : belongsTo(CustomerAddress::class, 'AddressID', 'cus_address_id')
 *   OrderController.php:183 ยืนยันของจริง: 'AddressID' => $customerAddress->cus_address_id
 *   → FK ของ AddressID ในตารางนี้ต้องชี้ไป CustomerAddress.cus_address_id ไม่ใช่ Address.AddressID
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Bookings', function (Blueprint $table) {
            $table->id('booking_id');

            // FK → Shops.shop_id (บังคับ — booking ต้องมีร้านเสมอ)
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                  ->references('shop_id')->on('Shops')
                  ->cascadeOnDelete();

            // FK → Users.user_id (บังคับ — booking ต้องมีลูกค้าเสมอ)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();

            // FK → Promotions.promotion_id (nullable — ไม่ใช่ทุก booking มีโปรโมชั่น)
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->foreign('promotion_id')
                  ->references('promotion_id')->on('Promotions')
                  ->nullOnDelete();

            // FK → CustomerAddress.cus_address_id (ดู docblock ด้านบน)
            // nullable เพราะ OrderDetailController::store() สร้าง Booking โดยไม่ส่งค่านี้
            $table->unsignedBigInteger('AddressID')->nullable();
            $table->foreign('AddressID')
                  ->references('cus_address_id')->on('CustomerAddress')
                  ->nullOnDelete();

            $table->date('purchase_date');                 // มี accessor/mutator ใน model แปลงเป็น Y-m-d
            $table->decimal('total_price', 10, 2);
            $table->decimal('amount_staff', 10, 2)->nullable();  // อยู่ใน $fillable แต่ไม่มีจุดไหนเซ็ตค่าจริง (โค้ดตาย)

            // ค่าที่เจอในโค้ด: pending, confirmed, partial paid, cancelled, completed
            $table->string('status')->default('pending');

            $table->boolean('hasOverrented')->default(false);

            $table->timestamp('created_at')->nullable();   // $timestamps=false, ประกาศเอง
            $table->date('pickup_date')->nullable();        // ไม่มีจุดไหนใช้งานจริงตอนนี้ แต่คงไว้ตาม $fillable
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Bookings');
    }
};
