<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $guarded = [];
    protected $casts = ["benefits" => "array", "faqs" => "array", "is_active" => "boolean"];

    public function products() { return $this->hasMany(Product::class); }

    public function scopeActive($q) { return $q->where("is_active", true)->orderBy("priority"); }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, "product_category_id");
    }
}
