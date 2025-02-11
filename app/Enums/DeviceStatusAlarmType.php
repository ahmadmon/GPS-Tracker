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
            self::SOS => 'درخواست کمک',
            self::NORMAL => 'عادی'
        };
    }


}
