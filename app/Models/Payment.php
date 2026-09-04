<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'Payments';
    protected $primaryKey = 'payment_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'payment_method',
        'total',
        'status',
        'booking_cycle',
        'booking_id',
        'created_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
