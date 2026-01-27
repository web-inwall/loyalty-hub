<?php

declare(strict_types=1);

use App\Domains\Loyalty\Strategies\GoldTierStrategy;
use App\Domains\Loyalty\Strategies\StandardStrategy;
use App\Domains\Wallet\ValueObjects\Money;
use App\Domains\Wallet\ValueObjects\Points;

test('StandardStrategy calculates 1 point per 10 currency units', function () {
    $strategy = new StandardStrategy;

    // 1000 minor units = 10 currency units => 1 point
    $points = $strategy->calculate(new Money(1000, 'USD'));
    expect($points->amount)->toBe(1);

    // 2500 minor units = 25 currency units => 2 points (floor(2.5))
    $points = $strategy->calculate(new Money(2500, 'USD'));
    expect($points->amount)->toBe(2);

    // 900 minor units = 9 currency units => 0 points
    $points = $strategy->calculate(new Money(900, 'USD'));
    expect($points->amount)->toBe(0);
});

test('GoldTierStrategy calculates 2 points per 10 currency units', function () {
    $strategy = new GoldTierStrategy;

    // 1000 minor units = 10 currency units => 2 points
    $points = $strategy->calculate(new Money(1000, 'USD'));
    expect($points->amount)->toBe(2);

    // 2500 minor units = 25 currency units => 4 points (floor(2.5) * 2)
    $points = $strategy->calculate(new Money(2500, 'USD'));
    expect($points->amount)->toBe(4);
});
