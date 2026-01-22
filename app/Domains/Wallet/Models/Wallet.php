<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Wallet model representing user balance.
 */
class Wallet extends Model
{
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
