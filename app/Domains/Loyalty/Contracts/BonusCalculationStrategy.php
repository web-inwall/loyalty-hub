<?php

declare(strict_types=1);

namespace App\Domains\Loyalty\Contracts;

use App\Domains\Wallet\ValueObjects\Money;
use App\Domains\Wallet\ValueObjects\Points;

interface BonusCalculationStrategy
{
    /**
     * Calculates bonus points based on purchase amount.
     */
    public function calculate(Money $purchaseAmount): Points;
}
