<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Models;

use App\Infrastructure\Models\Concerns\HasOptimisticLocking;
use Database\Factories\Domains\Wallet\Models\WalletFactory;
/**
 * Wallet model representing user balance.
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;
    use HasOptimisticLocking;

    protected static function newFactory()
    {
        return WalletFactory::new();
    }

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'version',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
        'version' => 'integer',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
