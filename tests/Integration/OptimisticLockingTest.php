<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Domains\Wallet\Models\Wallet;
use App\Infrastructure\Exceptions\StaleModelLockingException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('Optimistic Locking prevents overwriting stale data', function () {
    $wallet = Wallet::factory()->create(['balance' => 1000, 'version' => 1]);

    // Simulate concurrent update in DB
    DB::table('wallets')->where('id', $wallet->id)->update([
        'balance' => 1500,
        'version' => 2,
    ]);

    // Attempt to update stale model
    $wallet->balance = 800;

    expect(fn () => $wallet->save())->toThrow(StaleModelLockingException::class);

    // Verify DB remains untouched by the stale update
    $freshWallet = DB::table('wallets')->where('id', $wallet->id)->first();
    expect($freshWallet->balance)->toBe('1500.0000');
    expect($freshWallet->version)->toBe(2);
});
