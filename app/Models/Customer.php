<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];
    protected $casts = ["birthday" => "date", "is_vip" => "boolean"];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            $customer->code ??= "CUS-" . str_pad((string) ((static::withTrashed()->max("id") ?? 0) + 1), 5, "0", STR_PAD_LEFT);
            $customer->referral_code ??= strtoupper(str()->random(8));
        });
    }

    public function branch()    { return $this->belongsTo(Branch::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function addresses() { return $this->hasMany(CustomerAddress::class); }
    public function orders()    { return $this->hasMany(Order::class); }
    public function payments()  { return $this->hasMany(Payment::class); }
    public function loyalty()   { return $this->hasMany(LoyaltyTransaction::class); }
    public function tickets()   { return $this->hasMany(SupportTicket::class); }

    public function addPoints(int $points, string $type, ?string $description = null): void
    {
        $this->loyalty()->create(compact("points", "type", "description"));
        $this->increment("loyalty_points", $points);
        $this->refreshTier();
    }

    public function refreshTier(): void
    {
        $this->update(["loyalty_tier" => match (true) {
            $this->loyalty_points >= 5000 => "platinum",
            $this->loyalty_points >= 1500 => "gold",
            default => "silver",
        }]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(["name", "mobile", "email", "loyalty_points"])->logOnlyDirty();
    }
}
