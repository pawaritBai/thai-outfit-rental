<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThaiOutfitSizeAndColor extends Model
{
    use HasFactory;
    protected $table = 'Thaioutfit_SizeAndColor';
    protected $primaryKey = 'sizeDetail_id';
    protected $guarded = ['sizeDetail_id'];
    protected $fillable = ['outfit_id', 'size_id', 'color_id', 'amount'];
    
    public $timestamps = false;
    

    
    // Get the outfit this combination belongs to
    public function outfit()
    {
        return $this->belongsTo(ThaiOutfit::class, 'outfit_id', 'outfit_id');
    }
    
    // Get the size
    public function size()
    {
        return $this->belongsTo(ThaiOutfitSize::class, 'size_id', 'size_id');
    }
    
    // Get the color
    public function color()
    {
        return $this->belongsTo(ThaiOutfitColor::class, 'color_id', 'color_id');
    }
    
    // Get the color
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class,'sizeDetail_id', 'sizeDetail_id'); // หรือ field ที่ใช้เชื่อม
    
    }
    
}
