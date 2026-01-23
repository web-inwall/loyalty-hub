<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domains\Wallet\Actions\AccruePointsAction;
use App\Domains\Wallet\DTOs\AccruePointsData;
use App\Domains\Wallet\Models\Wallet;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WalletController extends Controller
{
    public function accrue(Request $request, AccruePointsAction $action): JsonResponse
    {
        $validated = $request->validate([
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string'],
            'transaction_reference' => ['required', 'string'],
        ]);

        $reference = $validated['transaction_reference'];
        $cacheKey = "wallet_transaction_{$reference}";

        if (Cache::has($cacheKey)) {
            return response()->json(['message' => 'Transaction already processed.'], 409);
        }

        $data = new AccruePointsData(
            walletId: (int) $validated['wallet_id'],
            amount: (int) $validated['amount'],
            reason: $validated['reason']
        );

        $action->execute($data);

        Cache::put($cacheKey, true, now()->addDay());

        /** @var Wallet $wallet */
        $wallet = Wallet::findOrFail($data->walletId);

        return response()->json([
            'balance' => $wallet->balance,
        ]);
    }
}
