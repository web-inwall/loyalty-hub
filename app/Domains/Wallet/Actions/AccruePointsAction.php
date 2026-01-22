<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Actions;

use App\Domains\Wallet\DTOs\AccruePointsData;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Exceptions\WalletNotFoundException;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AccruePointsAction
{
    /**
     * Safely accrues points to a wallet using pessimistic locking.
     */
    public function execute(AccruePointsData $data): void
    {
        DB::transaction(function () use ($data) {
            /** @var Wallet|null $wallet */
            $wallet = Wallet::lockForUpdate()->find($data->walletId);

            if (! $wallet) {
                throw new WalletNotFoundException("Wallet with ID {$data->walletId} not found.");
            }

            $wallet->increment('balance', $data->amount);

            $wallet->transactions()->create([
                'type' => TransactionType::Accrue,
                'amount' => $data->amount,
                'metadata' => ['reason' => $data->reason],
            ]);
        });
    }
}
