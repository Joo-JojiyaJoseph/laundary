<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PickupScheduled = "pickup_scheduled";
    case PickedUp        = "picked_up";
    case Washing         = "washing";
    case DryCleaning     = "dry_cleaning";
    case Ironing         = "ironing";
    case QualityCheck    = "quality_check";
    case Ready           = "ready";
    case OutForDelivery  = "out_for_delivery";
    case Delivered       = "delivered";

    public function label(): string
    {
        return match ($this) {
            self::PickupScheduled => "Pickup scheduled",
            self::PickedUp        => "Picked up",
            self::Washing         => "Washing",
            self::DryCleaning     => "Dry cleaning",
            self::Ironing         => "Ironing",
            self::QualityCheck    => "Quality check",
            self::Ready           => "Ready",
            self::OutForDelivery  => "Out for delivery",
            self::Delivered       => "Delivered",
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PickupScheduled => "calendar-days",
            self::PickedUp        => "truck",
            self::Washing         => "sparkles",
            self::DryCleaning     => "beaker",
            self::Ironing         => "fire",
            self::QualityCheck    => "shield-check",
            self::Ready           => "check-badge",
            self::OutForDelivery  => "map-pin",
            self::Delivered       => "home",
        };
    }

    /** Ordered pipeline used by the tracking timeline. */
    public static function pipeline(): array
    {
        return [
            self::PickupScheduled, self::PickedUp, self::Washing, self::DryCleaning,
            self::Ironing, self::QualityCheck, self::Ready, self::OutForDelivery, self::Delivered,
        ];
    }
}
