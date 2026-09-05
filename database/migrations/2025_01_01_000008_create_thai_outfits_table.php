<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง ThaiOutfits — ชุดไทยที่ร้านค้าลงขาย
 *
 * ที่มาของข้อมูล:
 *   app/Models/ThaiOutfit.php
 *     - protected $table = 'ThaiOutfits'
 *     - protected $primaryKey = 'outfit_id'
 *     - public $timestamps = false            → ไม่มี updated_at, created_at ประกาศเอง
 *     - protected $fillable = ['name','description','price','image','status',
 *                              'created_at','shop_id','depositfee','penaltyfee']
 *     - shop() : belongsTo(Shop, 'shop_id', 'shop_id')   → shop_id เป็น FK
 *
 *   app/Http/Controllers/OutfitController.php@store  validate():
 *     - name             required|string|max:255
 *     - description      required|string           (ไม่มี max)
 *     - price            required|numeric|min:0
 *     - depositfee       required|numeric|min:0
 *     - penaltyfee       required|numeric|min:0
 *     - image            nullable|image|...         (เก็บ path หลังอัปโหลด)
 *     - status           required|in:active,inactive
 *     - shop_id          required|exists:Shops,shop_id
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ThaiOutfits', function (Blueprint $table) {
            $table->id('outfit_id');

            $table->string('name');
            $table->text('description');                       // validate: string ไม่มี max
            $table->decimal('price', 10, 2);
            $table->decimal('depositfee', 10, 2);
            $table->decimal('penaltyfee', 10, 2);
            $table->string('image')->nullable();               // validate: nullable → เก็บ path รูป
            $table->string('status')->default('active');       // validate: in:active,inactive

            // FK → Shops.shop_id
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')
                  ->references('shop_id')->on('Shops')
                  ->cascadeOnDelete();                          // ลบร้าน → ลบชุดของร้านนั้นตาม

            // $timestamps = false แต่ $fillable มี created_at → ประกาศเองแบบ nullable (ไม่มี updated_at)
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ThaiOutfits');
    }
};
