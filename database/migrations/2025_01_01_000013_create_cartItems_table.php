<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง CartItems — สินค้าในตะกร้าของลูกค้า (1 แถว = 1 ชุด+ไซซ์+สี+รอบจอง)
 *
 * ที่มาของข้อมูล:
 *   app/Models/CartItem.php
 *     - protected $table = 'CartItems', $primaryKey = 'cart_item_id'
 *     - ไม่ได้ตั้ง $timestamps=false → timestamps เปิด (created_at + updated_at)
 *     - $fillable = ['quantity','created_at','purchased_at','outfit_id','userId',
 *                    'reservation_date','size_id','color_id','overent','sizeDetail_id']
 *     - relationship: outfit / size / color / user(userId->user_id) / thaioutfit_sizeandcolor(sizeDetail_id)
 *
 *   app/Http/Controllers/CartItemController.php
 *     - addToCart()  : CartItem::create([...]) บรรทัด 148 — ส่ง userId, outfit_id, size_id,
 *                      color_id, quantity, overent, sizeDetail_id, reservation_date
 *                      (ไม่ส่ง status / purchased_at / created_at)
 *     - index()      : ->where('status', 'INUSE')   ← column status มีจริง แต่ไม่อยู่ใน $fillable
 *   app/Http/Controllers/OrderController.php:223 : $item->status = 'REMOVED'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CartItems', function (Blueprint $table) {
            $table->id('cart_item_id');

            // FK → ThaiOutfits.outfit_id
            $table->unsignedBigInteger('outfit_id');
            $table->foreign('outfit_id')
                  ->references('outfit_id')->on('ThaiOutfits')
                  ->cascadeOnDelete();

            // FK → Users.user_id  (ชื่อ column คือ userId ตามโค้ดเดิม ไม่ใช่ user_id)
            $table->unsignedBigInteger('userId');
            $table->foreign('userId')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();

            // FK → Thaioutfit_Size.size_id
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')
                  ->references('size_id')->on('Thaioutfit_Size')
                  ->cascadeOnDelete();

            // FK → Thaioutfit_Color.color_id
            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')
                  ->references('color_id')->on('Thaioutfit_Color')
                  ->cascadeOnDelete();

            // FK → Thaioutfit_SizeAndColor.sizeDetail_id  (nullable — ของ overent อาจไม่มี combo นี้)
            $table->unsignedBigInteger('sizeDetail_id')->nullable();
            $table->foreign('sizeDetail_id')
                  ->references('sizeDetail_id')->on('Thaioutfit_SizeAndColor')
                  ->nullOnDelete();

            $table->integer('quantity')->default(1);
            $table->integer('overent')->default(0);          // flag 0/1 : ยืมเกินสต็อก
            $table->date('reservation_date');                 // วันที่จองใช้ชุด

            // column status ไม่อยู่ใน $fillable แต่โค้ดใช้จริง (INUSE / REMOVED)
            $table->string('status')->default('INUSE');

            $table->timestamp('purchased_at')->nullable();    // เซ็ตตอนแปลง cart -> order (create() ไม่ส่ง)
            $table->timestamps();                             // timestamps เปิดใน model
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CartItems');
    }
};
