<?php

use App\Enums\OrderStatus;

it('has a nine stage pipeline ending in delivered', function () {
    $pipeline = OrderStatus::pipeline();

    expect($pipeline)->toHaveCount(9)
        ->and($pipeline[0])->toBe(OrderStatus::PickupScheduled)
        ->and(end($pipeline))->toBe(OrderStatus::Delivered);
});
