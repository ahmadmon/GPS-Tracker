<?php

namespace App\Enums\Subscription;

use stdClass;

enum CancellationStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case REFUNDED = 'refunded';


    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    public function isRefunded(): bool
    {
        return $this === self::REFUNDED;
    }


    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'درحال بررسی',
            self::REJECTED => 'رد شده',
            self::REFUNDED => 'بازگشت داده شده',
        };
    }

    /**
     * @return stdClass
     */
    public function badge(): stdClass
    {
        return (object)match ($this) {
            self::PENDING => [
                'name' => $this->label(),
                'color' => 'warning'
            ],
            self::REJECTED => [
                'name' => $this->label(),
                'color' => 'danger'
            ],
            self::REFUNDED => [
                'name' => $this->label(),
                'color' => 'success'
            ]
        };
    }
}
