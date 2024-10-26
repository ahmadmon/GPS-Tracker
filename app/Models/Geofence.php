<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        $currentTime = Carbon::now()->format('H:i');

        if ($this->start_time <= $currentTime && $currentTime <= $this->end_time) {
            return true;
        }
        return false;
    }


    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class)->where('status', 1);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Device::class, 'id', 'id', 'device_id', 'user_id')->where('users.status',1);
    }
}
