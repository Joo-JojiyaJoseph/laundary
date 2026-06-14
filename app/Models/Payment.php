<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function order()    { return $this->belongsTo(Order::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function receivedBy() { return $this->belongsTo(User::class, "received_by"); }
}
