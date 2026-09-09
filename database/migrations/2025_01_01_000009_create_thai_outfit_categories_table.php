<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง ThaiOutfitCategories — pivot เชื่อม ThaiOutfits <-> OutfitCategories (many-to-many)
 *
 * ที่มาของข้อมูล:
 *   app/Models/ThaiOutfitCategory.php
 *     - protected $table = 'ThaiOutfitCategories'
 *     - protected $primaryKey = 'outfit_cate_id'
 *     - public $timestamps = false
 *     - protected $fillable = ['created_at', 'category_id', 'outfit_id']
 *     - outfit()   : belongsTo(ThaiOutfit, 'outfit_id', 'outfit_id')
 *     - category() : belongsTo(OutfitCategory, 'category_id', 'category_id')
 *
 *   app/Models/ThaiOutfit.php
 *     - categories() : belongsToMany(OutfitCategory::class, 'ThaiOutfitCategories', 'outfit_id', 'category_id')
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ThaiOutfitCategories', function (Blueprint $table) {
            $table->id('outfit_cate_id');

            // FK → ThaiOutfits.outfit_id
            $table->unsignedBigInteger('outfit_id');
            $table->foreign('outfit_id')
                  ->references('outfit_id')->on('ThaiOutfits')
                  ->cascadeOnDelete();      // ลบชุด → แถวเชื่อมนี้ไม่มีความหมายแล้ว

            // FK → OutfitCategories.category_id
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                  ->references('category_id')->on('OutfitCategories')
                  ->cascadeOnDelete();      // ลบ category → แถวเชื่อมนี้ไม่มีความหมายแล้ว

            // กันไม่ให้ผูกชุดเดียวกันกับ category เดียวกันซ้ำสองครั้ง
            $table->unique(['outfit_id', 'category_id']);

            // $timestamps = false แต่ $fillable มี created_at → ประกาศเองแบบ nullable
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ThaiOutfitCategories');
    }
};
