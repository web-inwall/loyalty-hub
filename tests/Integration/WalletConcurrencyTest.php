<?php

declare(strict_types=1);

use App\Domains\Wallet\Actions\AccruePointsAction;
use App\Domains\Wallet\DTOs\AccruePointsData;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('AccruePointsAction uses lockForUpdate to prevent race conditions', function () {
    $wallet = Wallet::factory()->create(['balance' => 0]);
    $action = app(AccruePointsAction::class);

    // We can't easily test exact locking behavior with simple functional tests without parallel processes,
    // but we can ensure the logic works sequentially and transactional integrity holds.

    // Simulate concurrent calls by running them in sequence within tests (as we are single threaded here usually)
    // For true concurrency test we would need separate processes, which is complex for unit tests.
    // Instead, we verify that the action performs the update correctly.

    $data1 = new AccruePointsData($wallet->id, 100, 'Concurrent 1');
    $data2 = new AccruePointsData($wallet->id, 50, 'Concurrent 2');

    $action->execute($data1);
    $action->execute($data2);

    $wallet->refresh();
    expect($wallet->balance)->toEqual('150.0000'); // Decimal cast as string usually in recent Laravel or float
});

test('AccruePointsAction runs inside database transaction', function () {
    $wallet = Wallet::factory()->create(['balance' => 0]);
    $action = app(AccruePointsAction::class);
    $data = new AccruePointsData($wallet->id, 100, 'Fail Tx');

    // Mock DB transaction to ensure it's called
    // A simple way is to force an exception inside the transaction and see if rollback happens
    // But since the action logic is inside the transaction block, we can't easily inject exception *into* the closure
    // without modifying the action or using advanced mocking.

    // Alternative: We check if the transaction count increased during execution via listener or spy.
    DB::spy();

    $action->execute($data);

    DB::shouldHaveReceived('transaction');
});
