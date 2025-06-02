<?php

namespace App\Enums\Wallet;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';


    public function isSuccessful(): bool
    {
        return $this === self::SUCCESS;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }


    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار پرداخت',
            self::SUCCESS => 'پرداخت موفق',
            self::FAILED => 'ناموفق یا لغو شده',
        };
    }

    /**
     * @return array
     */
    public function badge(): array
    {
        return match ($this) {
            self::PENDING => [
                'name' => 'در انتظار پرداخت',
                'color' => 'warning'
            ],
            self::SUCCESS => [
                'name' => 'پرداخت موفق',
                'color' => 'success'
            ],
            self::FAILED => [
                'name' => 'ناموفق یا لغو شده',
                'color' => 'danger'
            ],
            default => [
                'name' => '-',
                'color' => ''
            ]
        };
    }
}
