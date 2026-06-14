<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(fn (SupportTicket $t) => $t->ticket_no ??= "TKT-" . str_pad((string) ((static::max("id") ?? 0) + 1), 5, "0", STR_PAD_LEFT));
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function order()    { return $this->belongsTo(Order::class); }
    public function messages() { return $this->hasMany(SupportMessage::class); }
    public function agent()    { return $this->belongsTo(User::class, "assigned_to"); }
}
