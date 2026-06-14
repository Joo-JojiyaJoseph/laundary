<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(fn (OrderItem $item) => $item->tag_code ??= "TAG-" . strtoupper(str()->random(10)));
    }

    public function order()   { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function service() { return $this->belongsTo(Service::class); }
}
