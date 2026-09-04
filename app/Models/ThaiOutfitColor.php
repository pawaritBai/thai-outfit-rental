<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThaiOutfitColor extends Model
{
    protected $table = 'Thaioutfit_Color';
    protected $primaryKey = 'color_id';
    protected $guarded = ['color_id'];
    protected $fillable = ['color'];
    
    public $timestamps = false;
    
    // Get size and color combinations using this color
    public function sizeAndColors()
    {
        return $this->hasMany(ThaiOutfitSizeAndColor::class, 'color_id', 'color_id');
    }
}
