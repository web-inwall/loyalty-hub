<?php

declare(strict_types=1);

namespace App\Domains\Loyalty\Factories;

use App\Domains\Loyalty\Contracts\BonusCalculationStrategy;
use App\Domains\Loyalty\Strategies\GoldTierStrategy;
use App\Domains\Loyalty\Strategies\StandardStrategy;
use RuntimeException;

class BonusStrategyFactory
{
    /**
     * Resolves the bonus strategy based on user tier.
     */
    public function resolve(string $tier): BonusCalculationStrategy
    {
        return match (strtolower($tier)) {
            'gold' => new GoldTierStrategy,
            'standard' => new StandardStrategy,
            default => throw new RuntimeException("Unknown tier: {$tier}"),
        };
    }
}
