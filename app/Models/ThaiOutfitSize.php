<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThaiOutfitSize extends Model
{
    protected $table = 'Thaioutfit_Size';
    protected $primaryKey = 'size_id';
    protected $guarded = ['size_id'];
    protected $fillable = ['size'];
    
    public $timestamps = false;
    
    // Get size and color combinations using this size
    public function sizeAndColors()
    {
        return $this->hasMany(ThaiOutfitSizeAndColor::class, 'size_id', 'size_id');
    }
}
