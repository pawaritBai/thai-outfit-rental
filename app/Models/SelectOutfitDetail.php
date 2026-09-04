<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectOutfitDetail extends Model
{
    protected $table = 'SelectOutfitsDetails';
    protected $primaryKey = 'select_outfit_id';
    protected $guarded = ['select_outfit_id'];
    protected $fillable = ['status', 'quantity', 'created_at', 'chooser_id','customer_id','booking_id', 'outfit_id', 'size_id', 'color_id', 'sizeDetail_id'];
    
    // กำหนดให้ชัดเจนว่าไม่ใช้ timestamps
    public $timestamps = false;
    
    // นิยาม constant สำหรับค่า status ที่อนุญาต
    const STATUS_SELECTED = 'Selected';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_PENDING = 'Pending Selection';
    
    // Get the shop owner who chose this outfit
    public function chooser()
    {
        return $this->belongsTo(User::class, 'chooser_id', 'user_id');
    }
    
    // Get the customer this suggestion is for
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }
    
    // Get the booking this suggestion is for
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
    
    // Get the outfit that was suggested
    public function outfit()
    {
        return $this->belongsTo(ThaiOutfit::class, 'outfit_id', 'outfit_id');
    }
    
    // Get the size information
    public function size()
    {
        return $this->belongsTo(ThaiOutfitSize::class, 'size_id', 'size_id');
    }
    
    // Get the color information
    public function color()
    {
        return $this->belongsTo(ThaiOutfitColor::class, 'color_id', 'color_id');
    }
    
    // Get the size and color detail
    public function sizeAndColor()
    {
        return $this->belongsTo(ThaiOutfitSizeAndColor::class, 'sizeDetail_id', 'sizeDetail_id');
    }
}
