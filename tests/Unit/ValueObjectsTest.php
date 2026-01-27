<?php

declare(strict_types=1);

use App\Domains\Wallet\ValueObjects\Money;
use App\Domains\Wallet\ValueObjects\Points;

test('Money can be instantiated with valid values', function () {
    $money = new Money(100, 'USD');
    expect($money->amount)->toBe(100)
        ->and($money->currency)->toBe('USD');
});

test('Money throws exception for negative amount', function () {
    new Money(-100, 'USD');
})->throws(InvalidArgumentException::class);

test('Money throws exception for invalid currency code', function () {
    new Money(100, 'US'); // Too short
})->throws(InvalidArgumentException::class);

test('Points can be instantiated with valid values', function () {
    $points = new Points(50);
    expect($points->amount)->toBe(50);
});

test('Points throws exception for negative amount', function () {
    new Points(-5);
})->throws(InvalidArgumentException::class);
