<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CustomerAddress extends Model
{
    public $timestamps = false;


    protected $table = 'CustomerAddress';
    protected $primaryKey = 'cus_address_id';
    protected $guarded = ['cus_address_id'];
    protected $fillable = ['customer_id', 'AddressID', 'AddressType'];

    public function address()
{
    return $this->belongsTo(Address::class, 'AddressID', 'AddressID');
}


}
