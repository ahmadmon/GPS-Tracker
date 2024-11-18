<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Geofence extends Model
{
    use HasFactory;


    protected $guarded = ['id'];


    protected function casts(): array
    {
        return [
            'points' => 'array',
        ];
    }

    public function isGeofenceActive(): bool
    {
        $currentTime = now();

        if (isset($this->start_time) && isset($this->end_time)) {
            if ($currentTime->lt($this->start_time) && $currentTime->gt($this->end_time)) return true;
        }
        return false;
    }


    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class)->where('status', 1);
    }

    // Geofences Status (pivot table)
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class)->withPivot(['is_inside', 'lat', 'long','created_at']);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Device::class, 'id', 'id', 'device_id', 'user_id')->where('users.status',1);
    }
}
