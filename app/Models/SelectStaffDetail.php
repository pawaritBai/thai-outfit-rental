<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectStaffDetail extends Model
{
    protected $table = 'SelectStaffDetails';
    protected $primaryKey = 'select_staff_detail_id';
    protected $guarded = ['select_staff_detail_id'];
    const UPDATED_AT = null;
    public $timestamps = true;
    protected $fillable = ['customer_count','earning', 'created_at', 'select_service_id', 'staff_id'];

    public function selectService()
    {
        return $this->belongsTo(SelectService::class, 'select_service_id', 'select_service_id');
    }
}
