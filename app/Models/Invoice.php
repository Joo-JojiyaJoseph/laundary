<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];
    protected $casts = ["issued_at" => "datetime"];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            $invoice->invoice_no ??= "INV-" . str_pad((string) ((static::max("id") ?? 0) + 1), 5, "0", STR_PAD_LEFT);
            $invoice->tracking_token ??= bin2hex(random_bytes(24));
            $invoice->issued_at ??= now();
        });
    }

    public function order()    { return $this->belongsTo(Order::class); }
    public function customer() { return $this->belongsTo(Customer::class); }

    /** Public signed tracking URL embedded in the QR code. */
    public function trackingUrl(): string
    {
        return url("/track/{$this->order?->order_no}?t={$this->tracking_token}");
    }
}
