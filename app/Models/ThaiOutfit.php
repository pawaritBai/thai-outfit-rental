<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\ThaiOutfitSize;
use Carbon\Carbon;

class ThaiOutfit extends Model
{
    protected $table = 'ThaiOutfits';
    protected $primaryKey = 'outfit_id';
    protected $guarded = ['outfit_id'];
    protected $fillable = ['name', 'description', 'price', 'image', 'status', 'created_at', 'shop_id', 'depositfee', 'penaltyfee'];

    // Disable Laravel's automatic timestamp handling
    public $timestamps = false;

    // Get shop that owns this outfit
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    // ✅ NEW: ดึงโปรโมชั่นที่ยัง active และยังไม่หมดอายุ
    public function activePromotions()
    {
        return $this->shop
            ? $this->shop->promotions()
                ->where('is_active', 1)
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->get()
            : collect();
    }

    // ✅ NEW: ดึงโปรโมชั่นแรกที่ใช้ได้ (ใช้เป็น attribute ได้เลย)
    public function getCurrentPromotionAttribute()
    {
        return $this->shop
            ? $this->shop->promotions()
                ->where('is_active', 1)
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->first()
            : null;
    }

    // Get categories for this outfit
    public function categories()
    {
        return $this->belongsToMany(
            OutfitCategory::class,
            'ThaiOutfitCategories',
            'outfit_id',
            'category_id'
        );
    }

    // Get all size and color combinations for this outfit
    public function sizeAndColors()
    {
        return $this->hasMany(ThaiOutfitSizeAndColor::class, 'outfit_id', 'outfit_id')
            ->with(['size', 'color']);
    }

    // Get total stock (sum of all size/color combinations)
    public function getTotalStockAttribute()
    {
        return $this->sizeAndColors()->sum('amount') ?? 0;
    }

    protected static function boot()
    {
        parent::boot();

        // When deleting an outfit, also delete related size and color combinations
        static::deleting(function ($outfit) {
            $outfit->sizeAndColors()->delete();
        });
    }

    public function scopeFilterByCategory($query, $categoryIds)
    {
        if (!empty($categoryIds) && $categoryIds[0] !== '') {
            return $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('OutfitCategories.category_id', (array) $categoryIds);
            });
        }
        return $query;
    }

    public function scopeFilterBySize($query, $sizeIds)
    {
        if (!empty($sizeIds) && $sizeIds[0] !== '') {
            return $query->whereHas('sizeAndColors', function ($q) use ($sizeIds) {
                $q->whereIn('size_id', (array) $sizeIds);
            });
        }
        return $query;
    }

    public function scopeFilterByColor($query, $colorIds)
    {
        if (!empty($colorIds) && $colorIds[0] !== '') {
            return $query->whereHas('sizeAndColors', function ($q) use ($colorIds) {
                $q->whereIn('color_id', (array) $colorIds);
            });
        }
        return $query;
    }

    public function scopeFilterByStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }
}
