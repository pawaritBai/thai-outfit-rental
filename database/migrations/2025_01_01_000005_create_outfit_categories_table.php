<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * สร้างจาก app/Models/OutfitCategory.php
 *   $table='OutfitCategories', $primaryKey='category_id', $timestamps=false
 *   $fillable=['category_name', 'created_at']
 * + app/Http/Controllers/CategoryController.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('OutfitCategories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name');

            // model ปิด timestamps อัตโนมัติ แต่ $fillable มี created_at → ประกาศเองแบบ nullable
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('OutfitCategories');
    }
};
