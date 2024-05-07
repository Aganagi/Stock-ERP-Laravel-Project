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
        'brand_id',
        'product',
        'buy',
        'sell',
        'quantity',
        'image',
        'user_id'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_id');
    }

    public function hasOrder()
    {
        $order = Order::where('product_id', $this->id)->where('status', 'confirmed')->first();

        return $order ? true : false;
    }
    public function users(): HasMany 
    {
        return $this->hasMany(User::class, 'user_id');
    }
}