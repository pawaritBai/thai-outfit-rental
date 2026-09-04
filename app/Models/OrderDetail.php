<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'OrderDetails';
    protected $primaryKey = 'orderDetail_id';

    protected $fillable = [
        'quantity', 
        'total', 
        'booking_cycle', 
        'created_at', 
        'booking_id', 
        'cart_item_id',
        'deliveryOptions',
        'reservation_date',
    ];

    // Relationship with Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    // Relationship with CartItem
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class, 'cart_item_id', 'cart_item_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'booking_id');
    }

}
