<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'model',
        'price',
        'stock',
        'image',
        'description',
        'ram',
        'storage',
        'processor',
        'screen_size',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
}
