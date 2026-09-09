<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง Payments — การชำระเงินของ booking (แยกตามรอบ 1 / 2)
 *
 * ที่มาของข้อมูล:
 *   app/Models/Payment.php
 *     - $table = 'Payments', $primaryKey = 'payment_id'
 *     - const UPDATED_AT = null   → มี created_at แต่ "ไม่มี" updated_at
 *     - $fillable = ['payment_method','total','status','booking_cycle','booking_id','created_at']
 *     - booking() : belongsTo(Booking, 'booking_id')
 *
 *   จุดสร้าง Payment 3 ที่ (ทุกจุดส่ง total/status/booking_cycle/booking_id ครบ):
 *     OrderController.php:230   payment_method='paypal', status='unpaid'
 *     PaymentController.php:51  payment_method=$request->..., status='paid'
 *     PaymentController.php:128 payment_method=null, status='unpaid'   ← payment_method เป็น null ได้
 *   validate: payment_method in:credit_card,paypal | booking_cycle in:1,2
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Payments', function (Blueprint $table) {
            $table->id('payment_id');

            // FK → Bookings.booking_id
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')
                  ->references('booking_id')->on('Bookings')
                  ->cascadeOnDelete();

            $table->string('payment_method')->nullable();   // 'paypal' / 'credit_card' / null
            $table->decimal('total', 10, 2);
            $table->string('status');                        // 'paid' / 'unpaid'
            $table->integer('booking_cycle');               // 1 = รอบแรก, 2 = รอบสอง

            $table->timestamp('created_at')->nullable();     // const UPDATED_AT = null → ไม่มี updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Payments');
    }
};
