<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    protected $table = 'Bookings';
    protected $primaryKey = 'booking_id';
    protected $guarded = ['booking_id'];

    public $timestamps = false;

    protected $fillable = [
        'purchase_date', 
        'total_price',
        'amount_staff', 
        'status', 
        'hasOverrented', 
        'created_at', 
        'shop_id', 
        'promotion_id',
        'user_id',
        'pickup_date',
        'AddressID'
    ];

    // Define which attributes should be treated as dates
    protected $dates = [
        'purchase_date',
        'created_at',
        'pickup_date'
    ];

    // Handle date formatting when setting purchase_date
    public function setPurchaseDateAttribute($value)
    {
        if ($value) {
            $this->attributes['purchase_date'] = Carbon::parse($value)->format('Y-m-d');
        }
    }

    // Handle date formatting when getting purchase_date
    public function getPurchaseDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value);
        }
        return null;
    }

    // ความสัมพันธ์กับร้านค้า
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    // ความสัมพันธ์กับโปรโมชั่น
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'promotion_id');
    }

    // ความสัมพันธ์กับโปรโมชั่น
    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    // ความสัมพันธ์กับผู้ใช้
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // ความสัมพันธ์กับรายละเอียดออเดอร์
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'booking_id', 'booking_id');
    }

    // ความสัมพันธ์กับ selectservices
    public function selectService()
    {
        return $this->hasMany(SelectService::class, 'booking_id', 'booking_id');
    }

    // Relationship with address
    public function address()
    {
        return $this->belongsTo(Address::class, 'AddressID', 'AddressID');
    }

    // Add this new relationship method to the Booking model
    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'AddressID', 'cus_address_id');
    }

    /**
     * ตรวจสอบว่ามีชุดทดแทนหรือไม่
     */
    public function hasSuggestions()
    {
        return SelectOutfitDetail::where('booking_id', $this->booking_id)
            ->exists();
    }

    /**
     * ตรวจสอบว่ามีชุดทดแทนที่รอตอบรับหรือไม่
     */
    public function hasPendingSuggestions()
    {
        return SelectOutfitDetail::where('booking_id', $this->booking_id)
            ->where('status', 'Pending Selection')
            ->exists();
    }

    /**
     * ดึงข้อมูลชุดทดแทนทั้งหมด
     */
    public function outfitSuggestions()
    {
        return $this->hasMany(SelectOutfitDetail::class, 'booking_id', 'booking_id');
    }

}
