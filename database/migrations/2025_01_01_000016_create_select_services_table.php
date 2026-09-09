<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง SelectServices — บริการเสริมที่ลูกค้าเลือกใน 1 booking (ช่างภาพ / ช่างแต่งหน้า)
 *
 * ที่มาของข้อมูล:
 *   app/Models/SelectService.php
 *     - $table = 'SelectServices', $primaryKey = 'select_service_id'
 *     - timestamps เปิด (ไม่ตั้ง $timestamps=false, ไม่มี const UPDATED_AT)
 *     - $fillable = ['service_type','customer_count','created_at','booking_id','AddressID','reservation_date']
 *     - address() : belongsTo(Address, 'AddressID', 'AddressID')
 *     - booking() : belongsTo(Booking, 'booking_id', 'booking_id')
 *     - selectStaff(): เขียน belongsTo ไว้ แต่ FK จริงอยู่ฝั่ง SelectStaffDetail → ไม่มี column ที่นี่
 *
 *   จุดสร้างเดียว: app/Http/Controllers/OrderController.php:247 และ :258
 *     ส่ง service_type, customer_count, reservation_date, booking_id, AddressID (ครบ ยกเว้น created_at)
 *     reservation_date = new \DateTime("$date $time:00") → เก็บวันที่+เวลา
 *     service_type: 'photographer' | 'make-up artist'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('SelectServices', function (Blueprint $table) {
            $table->id('select_service_id');

            // FK → Bookings.booking_id
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')
                  ->references('booking_id')->on('Bookings')
                  ->cascadeOnDelete();

            // FK → Address.AddressID  (staffAddressId = Address.AddressID จริง ต่างจากเคส Bookings)
            $table->unsignedBigInteger('AddressID');
            $table->foreign('AddressID')
                  ->references('AddressID')->on('Address')
                  ->cascadeOnDelete();

            $table->string('service_type');                 // 'photographer' / 'make-up artist'
            $table->integer('customer_count');
            $table->dateTime('reservation_date');           // วันที่+เวลานัดบริการ

            $table->timestamps();                           // timestamps เปิดใน model
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SelectServices');
    }
};
