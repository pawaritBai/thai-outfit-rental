<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Thaioutfit_SizeAndColor', function (Blueprint $table) {
            $table->id('sizeDetail_id');

            // FK → ThaiOutfits.outfit_id
            $table->unsignedBigInteger('outfit_id');
            $table->foreign('outfit_id')
                  ->references('outfit_id')->on('ThaiOutfits')
                  ->cascadeOnDelete();      // ลบชุด → แถวเชื่อมนี้ไม่มีความหมายแล้ว

            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')
                  ->references('size_id')->on('Thaioutfit_Size')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')
                  ->references('color_id')->on('Thaioutfit_Color')
                  ->cascadeOnDelete();

            $table->integer('amount')->default(0);   // จำนวนชิ้นคงเหลือในสต็อก

            // กันไซซ์/สีเดียวกันของชุดเดียวกันถูกสร้างซ้ำ
            $table->unique(['outfit_id', 'size_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Thaioutfit_SizeAndColor');
    }
};
