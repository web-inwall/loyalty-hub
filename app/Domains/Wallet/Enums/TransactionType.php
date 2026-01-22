<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Enums;

/**
 * Transaction types for wallet operations.
 */
enum TransactionType: string
{
    case Accrue = 'accrue';
    case Redeem = 'redeem';
    case Expire = 'expire';
}
