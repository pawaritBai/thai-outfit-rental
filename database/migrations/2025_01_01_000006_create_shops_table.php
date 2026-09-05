<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * สร้างจาก app/Models/Shop.php
 *   $table='Shops', $primaryKey='shop_id'
 *   $fillable=['shop_name','shop_description','rental_terms','shop_owner_id',
 *              'created_at','status','is_newShop','AddressID']
 *   ไม่ได้ตั้ง $timestamps=false → มี created_at + updated_at
 * + app/Http/Controllers/ShopController.php@store  (validate + new Shop())
 *   status ที่เจอในโค้ด: inactive (รออนุมัติ), pending, active
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Shops', function (Blueprint $table) {
            $table->id('shop_id');

            $table->string('shop_name');          // validate: required|string|max:255
            $table->text('shop_description');     // validate: required|string  (ไม่มี max → text)
            $table->text('rental_terms');         // validate: required|string

            // FK → Users.user_id  (relationship Shop::user())
            $table->unsignedBigInteger('shop_owner_id');
            $table->foreign('shop_owner_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();            // ลบเจ้าของ → ลบร้านตาม

            // FK → Address.AddressID  (relationship Shop::address()) — nullable
            $table->unsignedBigInteger('AddressID')->nullable();
            $table->foreign('AddressID')
                  ->references('AddressID')->on('Address')
                  ->nullOnDelete();               // ลบที่อยู่ → เซ็ต null ไม่ลบร้าน

            $table->string('status')->default('inactive');
            $table->boolean('is_newShop')->default(true);

            $table->timestamps();                 // created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Shops');
    }
};
