<?php

declare(strict_types=1);

namespace App\Domains\Wallet\DTOs;

final readonly class AccruePointsData
{
    public function __construct(
        public int $walletId,
        public int $amount,
        public string $reason
    ) {}
}
