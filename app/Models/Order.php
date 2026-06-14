<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Events\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];
    protected $casts = [
        "status" => OrderStatus::class,
        "pickup_at" => "datetime",
        "delivery_expected_at" => "datetime",
        "delivered_at" => "datetime",
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_no ??= "LDS" . str_pad((string) ((static::withTrashed()->max("id") ?? 0) + 1), 5, "0", STR_PAD_LEFT);
            $order->delivery_otp ??= (string) random_int(100000, 999999);
        });
    }

    /** Public signed tracking URL (uses the order number, not the invoice). */
    public function trackingUrl(): string
    {
        $token = $this->invoice?->tracking_token;

        return url("/track/{$this->order_no}" . ($token ? "?t={$token}" : ""));
    }

    public function branch()     { return $this->belongsTo(Branch::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function rider()      { return $this->belongsTo(Rider::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function statusLogs() { return $this->hasMany(OrderStatusLog::class); }
    public function invoice()    { return $this->hasOne(Invoice::class); }
    public function payments()   { return $this->hasMany(Payment::class); }

    public function transitionTo(OrderStatus $status, ?User $by = null, ?string $remarks = null): void
    {
        $this->update(["status" => $status, "delivered_at" => $status === OrderStatus::Delivered ? now() : $this->delivered_at]);
        $this->statusLogs()->create(["status" => $status->value, "changed_by" => $by?->id, "remarks" => $remarks]);
        OrderStatusUpdated::dispatch($this->fresh(["customer", "statusLogs"]));
    }

    public function getOutstandingAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(["status", "total", "payment_status"])->logOnlyDirty();
    }
}
