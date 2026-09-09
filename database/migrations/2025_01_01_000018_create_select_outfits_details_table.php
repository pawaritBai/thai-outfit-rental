<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตาราง SelectOutfitsDetails — ข้อเสนอ "ชุดทดแทน" ที่ร้านเสนอให้ลูกค้าเมื่อชุดเดิมสต็อกไม่พอ
 *
 * ที่มาของข้อมูล:
 *   app/Models/SelectOutfitDetail.php
 *     - $table = 'SelectOutfitsDetails', $primaryKey = 'select_outfit_id', $timestamps = false
 *     - $fillable = ['status','quantity','created_at','chooser_id','customer_id','booking_id',
 *                    'outfit_id','size_id','color_id','sizeDetail_id']
 *     - const STATUS_SELECTED='Selected' / STATUS_REJECTED='Rejected' / STATUS_PENDING='Pending Selection'
 *
 *   จุดสร้างเดียว: app/Http/Controllers/BookingController.php:453 (suggestAlternatives)
 *     new SelectOutfitDetail() → เซ็ตทุก field แล้ว save() → ไม่มี field ไหน nullable
 *     chooser_id = Auth::id() (เจ้าของร้าน) / customer_id = $booking->user_id (ลูกค้า)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('SelectOutfitsDetails', function (Blueprint $table) {
            $table->id('select_outfit_id');

            // FK → Users.user_id  (เจ้าของร้านที่เลือกชุดทดแทน)
            $table->unsignedBigInteger('chooser_id');
            $table->foreign('chooser_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')
                  ->references('user_id')->on('Users')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')
                  ->references('booking_id')->on('Bookings')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('outfit_id');
            $table->foreign('outfit_id')
                  ->references('outfit_id')->on('ThaiOutfits')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')
                  ->references('size_id')->on('Thaioutfit_Size')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')
                  ->references('color_id')->on('Thaioutfit_Color')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('sizeDetail_id');
            $table->foreign('sizeDetail_id')
                  ->references('sizeDetail_id')->on('Thaioutfit_SizeAndColor')
                  ->cascadeOnDelete();


            $table->string('status')->default('Pending Selection');
            $table->integer('quantity');
            $table->timestamp('created_at')->nullable();     // $timestamps = false + $fillable มี created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SelectOutfitsDetails');
    }
};
