<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutfitCategory extends Model
{
    protected $table = 'OutfitCategories';
    protected $primaryKey = 'category_id';
    protected $guarded = ['category_id'];
    protected $fillable = ['category_name', 'created_at'];
    
    // Disable Laravel's automatic timestamp handling
    public $timestamps = false;
    
    // Get outfits in this category
    public function outfits()
    {
        return $this->belongsToMany(
            ThaiOutfit::class, 
            'ThaiOutfitCategories', 
            'category_id', 
            'outfit_id'
        );
    }
    
    // Get Thai outfit categories that use this category
    public function thaiOutfitCategories()
    {
        return $this->hasMany(ThaiOutfitCategory::class, 'category_id', 'category_id');
    }
}
