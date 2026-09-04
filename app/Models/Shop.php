<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'Shops';
    protected $primaryKey = 'shop_id';
    protected $guarded = ['shop_id'];
    protected $fillable = [
        'shop_name',
        'shop_description',
        'rental_terms',
        'shop_owner_id',
        'created_at',
        'status',
        'is_newShop',
        'AddressID'
    ];

    // ความสัมพันธ์กับ User - Fix the foreign key reference
    public function user()
    {
        return $this->belongsTo(User::class, 'shop_owner_id', 'user_id');
    }

    // ความสัมพันธ์กับ Address
    public function address()
    {
        return $this->belongsTo(Address::class, 'AddressID', 'AddressID');
    }

    // ความสัมพันธ์กับ Promotions
    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'shop_id', 'shop_id');
    }

    // ความสัมพันธ์กับ Bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'shop_id', 'shop_id');
    }
}
