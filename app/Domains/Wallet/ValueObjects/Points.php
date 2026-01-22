<?php

declare(strict_types=1);

namespace App\Domains\Wallet\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object representing loyalty points.
 */
final readonly class Points
{
    public function __construct(
        public int $amount
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Points amount cannot be negative.');
        }
    }
}
