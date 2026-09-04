<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * สร้างจาก app/Models/Address.php
 *   $table='Address', $primaryKey='AddressID', $timestamps=false
 *   const CREATED_AT='CreatedAt', const UPDATED_AT=null
 * + app/Http/Controllers/ShopController.php (Address::create([...]))
 * + app/Http/Controllers/ProfileController.php (customer address CRUD)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Address', function (Blueprint $table) {
            $table->id('AddressID');

            $table->string('HouseNumber')->nullable();
            $table->string('Street')->nullable();
            $table->string('Subdistrict')->nullable();
            $table->string('District')->nullable();
            $table->string('Province')->nullable();
            $table->string('PostalCode', 10)->nullable();

            // model ปิด timestamps อัตโนมัติ แต่ตั้งชื่อ created_at เป็น 'CreatedAt'
            $table->timestamp('CreatedAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Address');
    }
};
