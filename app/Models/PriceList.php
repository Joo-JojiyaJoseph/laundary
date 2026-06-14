<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $guarded = [];
    protected $casts = ["starts_at" => "date", "ends_at" => "date", "is_active" => "boolean"];

    public function product() { return $this->belongsTo(Product::class); }
    public function branch()  { return $this->belongsTo(Branch::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
