<?php

namespace App\Enums\Subscription;

use stdClass;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case EXPIRED = 'expired';
    case CANCELED = 'canceled';


    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function iExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }

    public function isInActive(): bool
    {
        return $this === self::INACTIVE;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'فعال',
            self::INACTIVE => 'غیرفعال',
            self::EXPIRED => 'منضی شده',
            self::CANCELED => 'لغو شده'
        };
    }


    /**
     * @return stdClass
     */
    public function badge(): stdClass
    {
        return (object)match ($this) {
            self::ACTIVE => [
                'name' => 'فعال',
                'color' => 'success'
            ],
            self::INACTIVE => [
                'name' => 'غیرفعال',
                'color' => 'warning'
            ],
            self::EXPIRED => [
                'name' => 'منضی شده',
                'color' => 'dark'
            ],
            self::CANCELED => [
                'name' => 'لغو شده',
                'color' => 'danger'
            ]
        };
    }
}
