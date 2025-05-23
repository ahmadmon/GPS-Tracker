<?php

namespace App\Models;

use App\Enums\DeviceBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    protected function casts(): array
    {
        return [
            'brand' => DeviceBrand::class,
        ];
    }

    public function lastLocation(): Trip|null
    {
        return Trip::where('device_id', $this->id)->orderByDesc('id')->first() ?? null;
    }

    /**
     * @return DeviceStatus|null
     */
    public function lastStatus(): DeviceStatus|null
    {
        return DeviceStatus::where('device_id', $this->id)->latest()->first() ?? null;
    }


    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function geofences(): HasMany
    {
        return $this->hasMany(Geofence::class);
    }

    public function deviceStatus(): HasOne
    {
        return $this->hasOne(DeviceStatus::class, 'device_id');
    }
}
