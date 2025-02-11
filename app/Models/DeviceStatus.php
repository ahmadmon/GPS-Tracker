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

    protected function voltageLevel(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ((int)$this->voltage_level) {
                    0 => 'بدون برق',
                    1 => 'باتری خیلی ضعیف',
                    2 => 'باتری خیلی کم',
                    3 => 'باتری کم (قابل استفاده)',
                    4 => 'باتری متوسط',
                    5 => 'باتری زیاد',
                    6 => 'باتری خیلی زیاد',
                    7 => 'نامشخص',

                };
            }
        );
    }

    protected function signalLevel(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ((int)$this->signal_level) {
                    0 => 'بدون سیگنال',
                    1 => 'سیگنال خیلی ضعیف',
                    2 => 'سیگنال ضعیف',
                    3 => 'سیگنال خوب',
                    4 => '',
                    5 => 'نامشخص',

                };
            }
        );
    }


    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
