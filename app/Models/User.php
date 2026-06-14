<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $guarded = [];
    protected $hidden = ["password", "remember_token", "two_factor_secret"];
    protected $casts = [
        "email_verified_at" => "datetime",
        "password" => "hashed",
        "two_factor_enabled" => "boolean",
        "is_active" => "boolean",
    ];

    public function branch()       { return $this->belongsTo(Branch::class); }
    public function rider()        { return $this->hasOne(Rider::class); }
    public function customer()     { return $this->hasOne(Customer::class); }
    public function loginHistory() { return $this->hasMany(LoginHistory::class); }
    public function deviceTokens() { return $this->hasMany(DeviceToken::class); }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(["name", "email", "mobile", "is_active"])->logOnlyDirty();
    }
}
