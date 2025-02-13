<?php

namespace App\Enums;

enum DeviceStatusAlarmType: string
{
    case SHOCK = 'shock';
    case POWER_CUT = 'power_cut';
    case LOW_BATTERY = 'low_battery';
    case SOS = 'sos';
    case NORMAL = 'normal';

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SHOCK => 'ضربه و لرزش',
            self::POWER_CUT => 'قطع برق',
            self::LOW_BATTERY => 'شارژ کم',
            self::SOS => '(SOS) درخواست کمک',
            self::NORMAL => 'عادی'
        };
    }

    /**
     * @return array
     */
    public function badge(): array
    {
        return match ($this) {
            self::SHOCK => [
                'name' => 'ضربه و لرزش',
                'color' => 'danger'
            ],
            self::POWER_CUT => [
                'name' => 'قطع برق',
                'color' => 'warning'
            ],self::LOW_BATTERY => [
                'name' => 'شارژ کم',
                'color' => 'dark'
            ],
            self::SOS => [
                'name' => '(SOS) درخواست کمک',
                'color' => 'danger'
            ],
            self::NORMAL => [
                'name' => 'عادی',
                'color' => 'success'
            ],
            default => [
                'name' => '-',
                'color' => ''
            ]
        };
    }
}
