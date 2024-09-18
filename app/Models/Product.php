<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'regular_price',
        'sales_price',
        'description',
        'UPC',
        'image',
        'images',
        'sales_number',
        'is_active',
        'is_new_arrival',
        'category_id',
        'brand_id'
    ];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brands(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
