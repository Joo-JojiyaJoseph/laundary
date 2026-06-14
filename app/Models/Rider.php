<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Broadcast;

class Rider extends Model
{
    protected $guarded = [];
    protected $casts = ["is_online" => "boolean", "location_updated_at" => "datetime"];

    public function user()   { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function orders() { return $this->hasMany(Order::class); }

    public function updateLocation(float $lat, float $lng): void
    {
        $this->update(["current_lat" => $lat, "current_lng" => $lng, "location_updated_at" => now()]);
        broadcast(new \App\Events\RiderLocationUpdated($this))->toOthers();
    }
}
