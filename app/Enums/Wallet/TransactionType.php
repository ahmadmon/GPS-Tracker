<?php

namespace App\Enums\Wallet;

enum TransactionType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';

    public function isCredit(): bool
    {
        return $this === self::CREDIT;
    }

    public function isDebit(): bool
    {
        return $this === self::DEBIT;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::CREDIT => 'واریز',
            self::DEBIT => 'برداشت',
        };
    }

    /**
     * @return array
     */
    public function badge(): array
    {
        return match ($this) {
            self::CREDIT => [
                'name' => 'واریز',
                'color' => 'success'
            ],
            self::DEBIT => [
                'name' => 'برداشت',
                'color' => 'danger'
            ],
            default => [
                'name' => '-',
                'color' => ''
            ]
        };
    }
}
