<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address',
        'note',
        'total_amount',
        'status',
        'payment_method',
        'payment_token',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    protected $appends = ['items_summary'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getItemsSummaryAttribute()
    {
        // prevent error if relationship not loaded
        if (!$this->relationLoaded('orderItems')) {
            $this->load('orderItems.product');
        }

        return $this->orderItems->map(function ($item) {
            return [
                'product_name' => optional($item->product)->name,
                'quantity' => $item->quantity,
            ];
        });
    }
}
