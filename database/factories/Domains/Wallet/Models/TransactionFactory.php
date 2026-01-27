<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Wallet\Models;

use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),
            'type' => TransactionType::Accrue,
            'amount' => 100,
            'metadata' => [],
            'created_at' => now(),
        ];
    }
}
