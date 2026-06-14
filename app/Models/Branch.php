<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = ["working_hours" => "array", "is_active" => "boolean"];

    public function users()     { return $this->hasMany(User::class); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function orders()    { return $this->hasMany(Order::class); }
    public function riders()    { return $this->hasMany(Rider::class); }
}
