<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง OrderDetails — รายการสินค้าในคำสั่งจอง 1 บรรทัด (1 booking มีได้หลาย order detail)
 *
 * ที่มาของข้อมูล:
 *   app/Models/OrderDetail.php
 *     - $table = 'OrderDetails', $primaryKey = 'orderDetail_id'
 *     - ไม่ได้ตั้ง $timestamps=false → timestamps เปิด
 *     - $fillable = ['quantity','total','booking_cycle','created_at','booking_id',
 *                    'cart_item_id','deliveryOptions','reservation_date']
 *     - booking() : belongsTo(Booking, 'booking_id'), cartItem() : belongsTo(CartItem, 'cart_item_id')
 *
 *   migration เดิม _needs_rewrite/ 2 ไฟล์ (add_timestamps..., add_updated_at...) พยายามเพิ่ม
 *   created_at/updated_at ทีหลัง → เดิมตารางนี้ไม่มี timestamps เรารวบใส่ตั้งแต่แรกเลย
 *
 *   จุดสร้าง OrderDetail 3 ที่ (ใช้เทียบหา field ที่ nullable):
 *     OrderController.php:211        — ไม่ส่ง created_at
 *     OrderDetailController.php:195  — ไม่ส่ง reservation_date   ← ต้อง nullable
 *     ProfileController.php:389      — ส่งครบ
 *   deliveryOptions: validate in:'self pick-up','delivery' แต่โค้ดยังใส่ค่า 'default' ด้วย
 *   booking_cycle: validate in:1,2 (รอบชำระเงิน)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('OrderDetails', function (Blueprint $table) {
            $table->id('orderDetail_id');

            // FK → Bookings.booking_id
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')
                  ->references('booking_id')->on('Bookings')
                  ->cascadeOnDelete();

            // FK → CartItems.cart_item_id
            $table->unsignedBigInteger('cart_item_id');
            $table->foreign('cart_item_id')
                  ->references('cart_item_id')->on('CartItems')
                  ->cascadeOnDelete();

            $table->integer('quantity')->default(1);
            $table->decimal('total', 10, 2)->nullable();       // null เมื่อ overent == 1 (OrderController:218)
            $table->integer('booking_cycle')->default(1);      // 1 = รอบแรก, 2 = รอบสอง
            $table->string('deliveryOptions')->default('default');  // 'self pick-up' / 'delivery' / 'default'
            $table->date('reservation_date')->nullable();      // OrderDetailController:195 ไม่ส่งค่านี้

            $table->timestamps();                              // timestamps เปิดใน model
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('OrderDetails');
    }
};
