<?php

namespace App\Enums\Subscription\Plan;

use stdClass;

enum PlanType: string
{
    case PERSONAL = 'personal';
    case COMPANY = 'company';
    case BOTH = 'both';

    public function isPersonal(): bool
    {
        return $this === self::PERSONAL;
    }

    public function isCompany(): bool
    {
        return $this === self::COMPANY;
    }

    public function isBoth(): bool
    {
        return $this === self::BOTH;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PERSONAL => 'شخصی',
            self::COMPANY => 'سازمانی',
            self::BOTH => 'عمومی'
        };
    }

    /**
     * @return stdClass
     */
    public function badge(): stdClass
    {
        return (object)match ($this) {
            self::PERSONAL => [
                'name' => 'شخصی',
                'color' => 'warning'
            ],
            self::COMPANY => [
                'name' => 'سازمانی',
                'color' => 'info'
            ],
            self::BOTH => [
                'name' => 'عمومی',
                'color' => 'primary'
            ]
        };
    }

    /**
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array
     */
    public static function toSelectOptions(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->label()
            ];
        }, self::cases());
    }
}
