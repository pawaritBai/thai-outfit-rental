<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectService extends Model
{
    protected $table = 'SelectServices';
    protected $primaryKey = 'select_service_id';
    protected $guarded = ['select_service_id'];
    protected $fillable = ['service_type', 'customer_count', 'created_at', 'booking_id','AddressID','reservation_date'];

    public function address()
    {
        return $this->belongsTo(Address::class, 'AddressID', 'AddressID');
    }
    public function selectStaff()
    {
        return $this->belongsTo(SelectStaffDetail::class, 'select_service_id', 'select_service_id');
    }
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
