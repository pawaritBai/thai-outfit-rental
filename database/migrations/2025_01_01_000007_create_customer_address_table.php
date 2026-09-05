<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง CustomerAddress — เก็บที่อยู่ของลูกค้า (1 ลูกค้ามีได้หลายที่อยู่)
 *
 * ที่มาของข้อมูล:
 *   app/Models/CustomerAddress.php
 *     - protected $table = 'CustomerAddress'
 *     - protected $primaryKey = 'cus_address_id'
 *     - public $timestamps = false           → ไม่มี created_at / updated_at
 *     - protected $fillable = ['customer_id', 'AddressID', 'AddressType']
 *     - address() : belongsTo(Address, 'AddressID', 'AddressID')   → AddressID เป็น FK
 *
 *   app/Http/Controllers/ProfileController.php
 *     - storeAddress()  : CustomerAddress::create(['customer_id' => Auth::id(), 'AddressID' => ..., ...])
 *                         → customer_id เก็บ user id ของลูกค้า → FK ไป Users.user_id
 *     - editAddress()/deleteAddress() : เช็ค $cusAddress->customer_id != Auth::id()
 *
 * ⚠️ ความไม่ตรงกันในโค้ดเดิม (latent bug):
 *   ProfileController::storeAddress() ส่ง 'AddressName' => $request->AddressName
 *   แต่ $fillable ของ model มีแค่ 'AddressType' (ไม่มี AddressName)
 *   → ค่า AddressName ถูก Eloquent ตัดทิ้งเงียบ ๆ เพราะไม่อยู่ใน $fillable
 *   ที่นี่ยึดตาม $fillable ของ model = 'AddressType' ไว้ก่อน
 *   ตอน refactor ค่อยตัดสินใจว่าจะแก้ controller หรือ rename column
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CustomerAddress', function (Blueprint $table) {
            // $primaryKey = 'cus_address_id'  (จาก model)
            $table->id('cus_address_id');

            // FK → Users.user_id
            // customer_id ใน $fillable + ProfileController เซ็ตเป็น Auth::id()
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();          // ลบลูกค้า → ที่อยู่ของเขาหายตาม

            // FK → Address.AddressID
            // จาก relationship CustomerAddress::address() และ storeAddress() ที่ผูก $address->AddressID
            $table->unsignedBigInteger('AddressID');
            $table->foreign('AddressID')
                  ->references('AddressID')->on('Address')
                  ->cascadeOnDelete();          // record นี้คือ "ตัวเชื่อม" ถ้า address หาย ก็ไร้ความหมาย

            // จาก $fillable ของ model — ป้ายกำกับที่อยู่ (บ้าน / ที่ทำงาน ฯลฯ) ไม่บังคับ
            $table->string('AddressType')->nullable();

            // $timestamps = false และ $fillable ไม่มี created_at → ไม่ประกาศ column เวลา
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CustomerAddress');
    }
};
