<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = ["is_active" => "boolean"];

    public function service()    { return $this->belongsTo(Service::class); }
    public function category()   { return $this->belongsTo(ProductCategory::class, "product_category_id"); }
    public function priceLists() { return $this->hasMany(PriceList::class); }

    /** Resolve effective price: customer > vip > seasonal/promo > branch > base. */
    public function priceFor(?Branch $branch = null, ?Customer $customer = null): float
    {
        $today = now()->toDateString();
        $lists = $this->priceLists()->where("is_active", true)
            ->where(fn ($q) => $q->whereNull("starts_at")->orWhere("starts_at", "<=", $today))
            ->where(fn ($q) => $q->whereNull("ends_at")->orWhere("ends_at", ">=", $today))
            ->get();

        if ($customer && ($p = $lists->firstWhere("customer_id", $customer->id))) return (float) $p->price;
        if ($customer?->is_vip && ($p = $lists->firstWhere("type", "vip"))) return (float) $p->price;
        if ($p = $lists->whereIn("type", ["seasonal", "promo"])->first()) return (float) $p->price;
        if ($branch && ($p = $lists->where("branch_id", $branch->id)->firstWhere("type", "branch"))) return (float) $p->price;

        return (float) $this->price;
    }
}
