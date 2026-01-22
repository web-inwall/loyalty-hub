<?php

declare(strict_types=1);

namespace App\Domains\Loyalty\Strategies;

use App\Domains\Loyalty\Contracts\BonusCalculationStrategy;
use App\Domains\Wallet\ValueObjects\Money;
use App\Domains\Wallet\ValueObjects\Points;

class GoldTierStrategy implements BonusCalculationStrategy
{
    public function calculate(Money $purchaseAmount): Points
    {
        // 2 points for every 10 currency units (1000 minor units).
        $points = (int) floor($purchaseAmount->amount / 1000) * 2;

        return new Points($points);
    }
}
