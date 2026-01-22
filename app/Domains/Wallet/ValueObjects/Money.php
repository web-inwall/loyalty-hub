<?php

declare(strict_types=1);

namespace App\Domains\Wallet\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object representing a monetary value.
 */
final readonly class Money
{
    /**
     * @param  int  $amount  Amount in minor units (e.g., cents)
     * @param  string  $currency  ISO 4217 currency code
     */
    public function __construct(
        public int $amount,
        public string $currency
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative.');
        }

        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be a valid ISO 4217 code.');
        }
    }
}
