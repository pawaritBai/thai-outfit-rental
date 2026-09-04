<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'CartItems';
    protected $primaryKey = 'cart_item_id';
    protected $guarded = ['cart_item_id'];
    protected $fillable = ['quantity', 'created_at', 'purchased_at', 'outfit_id', 'userId', 'reservation_date', 'size_id', 'color_id', 'overent','sizeDetail_id'];

    public function outfit()
    {
        return $this->belongsTo(ThaiOutfit::class, 'outfit_id', 'outfit_id');
    }

    // ความสัมพันธ์กับ Size
    public function size()
    {
        return $this->belongsTo(ThaiOutfitSize::class, 'size_id', 'size_id');
    }

    // ความสัมพันธ์กับ Color
    public function color()
    {
        return $this->belongsTo(ThaiOutfitColor::class, 'color_id', 'color_id');
    }

    // Relations with User
    public function user()
    {
        return $this->belongsTo(User::class,'userId', 'user_id'); // หรือ field ที่ใช้เชื่อม
    }
    
    // Relations with thaioutfit_sizeandcolor
    public function thaioutfit_sizeandcolor()
    {
        return $this->belongsTo(ThaiOutfitSizeAndColor::class,'sizeDetail_id', 'sizeDetail_id'); // หรือ field ที่ใช้เชื่อม
    }
    
    // เพิ่มความสัมพันธ์กับ OrderDetail
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'cart_item_id', 'cart_item_id');
    }
    
    // หรือถ้ามีความสัมพันธ์ many-to-many กับ OrderDetail
    // public function orderDetail()
    // {
    //     return $this->hasMany(OrderDetail::class, 'cart_item_id', 'cart_item_id');
    // }
}
