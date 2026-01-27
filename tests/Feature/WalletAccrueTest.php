<?php

declare(strict_types=1);

use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Enums\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('POST /api/wallet/accrue accrues points successfully', function () {
    $wallet = Wallet::factory()->create(['balance' => 0]);
    $payload = [
        'wallet_id' => $wallet->id,
        'amount' => 100,
        'reason' => 'Test Bonus',
        'transaction_reference' => 'ref-123',
    ];

    $response = $this->postJson('/api/wallet/accrue', $payload);

    $response->assertStatus(200)
        ->assertJson(['balance' => 100]);

    $this->assertDatabaseHas('wallets', [
        'id' => $wallet->id,
        'balance' => 100,
    ]);

    $this->assertDatabaseHas('transactions', [
        'wallet_id' => $wallet->id,
        'amount' => 100,
        'type' => TransactionType::Accrue->value,
        'metadata' => json_encode(['reason' => 'Test Bonus']),
    ]);
});

test('POST /api/wallet/accrue handles idempotency', function () {
    $wallet = Wallet::factory()->create(['balance' => 0]);
    $payload = [
        'wallet_id' => $wallet->id,
        'amount' => 50,
        'reason' => 'Double Dip',
        'transaction_reference' => 'unique-ref-456',
    ];

    $this->postJson('/api/wallet/accrue', $payload)->assertStatus(200);
    $response = $this->postJson('/api/wallet/accrue', $payload);

    $response->assertStatus(409)
        ->assertJson(['message' => 'Transaction already processed.']);

    $this->assertDatabaseHas('wallets', [
        'id' => $wallet->id,
        'balance' => 50, // Should be 50, not 100
    ]);
    
    // Ensure only one transaction record created
    expect($wallet->transactions()->count())->toBe(1);
});

test('POST /api/wallet/accrue validation fails on invalid data', function () {
    $response = $this->postJson('/api/wallet/accrue', []);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['wallet_id', 'amount', 'reason', 'transaction_reference']);
});

test('POST /api/wallet/accrue returns 404/422 for non-existent wallet', function () {
    $payload = [
        'wallet_id' => 999999,
        'amount' => 100,
        'reason' => 'Ghost',
        'transaction_reference' => 'ref-999',
    ];

    $response = $this->postJson('/api/wallet/accrue', $payload);
    
    // Based on `exists:wallets,id` validation rule, Laravel typically returns 422
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['wallet_id']);
});
