<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'Promotions';
    protected $primaryKey = 'promotion_id';
    protected $guarded = ['promotion_id'];
    
    // Disable timestamps (created_at and updated_at)
    public $timestamps = false;
    
    protected $fillable = [
        'promotion_name', 
        'description',
        'promotion_code',
        'discount_amount', 
        'start_date', 
        'end_date', 
        'is_active',
        'created_at', 
        'shop_id'
    ];

    // ความสัมพันธ์กับร้านค้า
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    // ความสัมพันธ์กับการจอง
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'promotion_id', 'promotion_id');
    }

    // ตรวจสอบว่าโปรโมชั่นยังใช้งานได้หรือไม่
    public function isValid()
    {
        $today = now()->format('Y-m-d');
        return $this->is_active && 
               $today >= $this->start_date && 
               $today <= $this->end_date;
    }

    
}
