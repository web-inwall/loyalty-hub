<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Models;

use App\Domains\Wallet\Enums\TransactionType;
use Database\Factories\Domains\Wallet\Models\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * Transaction model for wallet history.
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }

    public const UPDATED_AT = null;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:4',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
