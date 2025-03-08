<?php

namespace App\Models;

use App\Enums\DeviceStatusAlarmType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceStatus extends Model
{
    protected $guarded = ['id'];


    protected function casts(): array
    {
        return [
            'alarm_type' => DeviceStatusAlarmType::class
        ];
    }

    protected function batteryStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                $voltageLevel = (int)$this->voltage_level;

                $text = match ($voltageLevel) {
                    0 => 'بدون شارژ',
                    1 => 'خیلی ضعیف',
                    2 => 'خیلی کم',
                    3 => 'کم (قابل استفاده)',
                    4 => 'متوسط',
                    5 => 'زیاد',
                    6 => 'خیلی زیاد',
                    7 => 'نامشخص',

                };

                $iconClass = match ($voltageLevel) {
                    0 => 'battery-empty', // Empty
                    1, 2, 3 => 'battery-quarter text-danger', // Low
                    4 => 'battery-half text-warning', // Half
                    5 => 'battery-three-quarter text-success', // Good
                    6 => 'battery-full text-success',  //Very Good
                    default => 'battery-unknown text-secondary',
                };

                return [
                    'text' => $text,
                    'iconClass' => $iconClass
                ];
            }
        );
    }

    protected function signalStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                $signalLevel = (int)$this->signal_level;

                $text = match ((int)$this->signal_level) {
                    0 => 'بدون سیگنال',
                    1 => 'خیلی ضعیف',
                    2 => 'ضعیف',
                    3 => 'خوب',
                    4 => 'خیلی خوب',
                    5 => 'نامشخص',

                };

                $color = match ($signalLevel) {
                    0, 1 => 'text-danger',
                    2 => 'text-warning',
                    3, 4 => 'text-success',
                    default => 'signal-unknown text-secondary',
                };

                return [
                    'text' => $text,
                    'color' => $color
                ];
            }
        );
    }


    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
