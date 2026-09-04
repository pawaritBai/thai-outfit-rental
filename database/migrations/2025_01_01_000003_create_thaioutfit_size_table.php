<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * สร้างจาก app/Models/ThaiOutfitSize.php
 *   $table='Thaioutfit_Size', $primaryKey='size_id', $timestamps=false, $fillable=['size']
 * เป็นตาราง lookup (S / M / L / XL ...) ต้อง seed ข้อมูลเองภายหลัง
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Thaioutfit_Size', function (Blueprint $table) {
            $table->id('size_id');
            $table->string('size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Thaioutfit_Size');
    }
};
