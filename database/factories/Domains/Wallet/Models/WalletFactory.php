<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Wallet\Models;

use App\Domains\Wallet\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance' => 0,
            'currency' => 'RUB',
            'version' => 1,
        ];
    }
}
