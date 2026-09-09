<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง SelectStaffDetails — งานที่ staff (ช่างภาพ/ช่างแต่งหน้า) รับจาก SelectServices 1 รายการ
 *
 * ที่มาของข้อมูล:
 *   app/Models/SelectStaffDetail.php
 *     - $table = 'SelectStaffDetails', $primaryKey = 'select_staff_detail_id'
 *     - const UPDATED_AT = null; $timestamps = true;  → มีแค่ created_at
 *     - $fillable = ['customer_count','earning','created_at','select_service_id','staff_id']
 *     - selectService() : belongsTo(SelectService, 'select_service_id', 'select_service_id')
 *
 *   จุดสร้าง: SelectServiceController.php:89 และ StaffController.php:144
 *     new SelectStaffDetail() → เซ็ต select_service_id, staff_id, customer_count, earning แล้ว save()
 *
 *   column ที่โค้ดใช้แต่ไม่อยู่ใน $fillable (เซ็ตผ่าน property + save() ตรง ๆ):
 *     finished_time  — StaffController.php:25/43/178..220 (null = ยังไม่เสร็จงาน)
 *     service_info   — StaffController.php:44
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('SelectStaffDetails', function (Blueprint $table) {
            $table->id('select_staff_detail_id');

            // FK → SelectServices.select_service_id
            $table->unsignedBigInteger('select_service_id');
            $table->foreign('select_service_id')
                  ->references('select_service_id')->on('SelectServices')
                  ->cascadeOnDelete();

            // FK → Users.user_id  (staff ที่รับงาน)
            $table->unsignedBigInteger('staff_id');
            $table->foreign('staff_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();

            $table->integer('customer_count');
            $table->decimal('earning', 10, 2);

            $table->dateTime('finished_time')->nullable();   // เซ็ตตอนกดเสร็จงาน (StaffController::finishJob)
            $table->text('service_info')->nullable();

            $table->timestamp('created_at')->nullable();     // const UPDATED_AT = null → ไม่มี updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SelectStaffDetails');
    }
};
