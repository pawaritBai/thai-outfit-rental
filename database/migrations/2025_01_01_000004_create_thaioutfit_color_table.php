<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * สร้างจาก app/Models/ThaiOutfitColor.php
 *   $table='Thaioutfit_Color', $primaryKey='color_id', $timestamps=false, $fillable=['color']
 * เป็นตาราง lookup (แดง / น้ำเงิน ...) ต้อง seed ข้อมูลเองภายหลัง
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Thaioutfit_Color', function (Blueprint $table) {
            $table->id('color_id');
            $table->string('color');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Thaioutfit_Color');
    }
};
