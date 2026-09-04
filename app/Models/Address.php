<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'Address';
    protected $primaryKey = 'AddressID';
    public $timestamps = false; // Change this to false
    
    // If you still want to use CreatedAt, you can customize the field name
    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null; // This tells Laravel you don't have an updated_at column
    
    protected $fillable = [
        'HouseNumber',
        'Street',
        'Subdistrict',
        'District',
        'Province',
        'PostalCode'
    ];

    // Relationship with Shop
    public function shops()
    {
        return $this->hasMany(Shop::class, 'AddressID', 'AddressID');
    }

    public function address()
{
    return $this->belongsTo(Address::class, 'AddressID', 'AddressID');
}

}
