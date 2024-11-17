<?php

namespace App\Models;

use App\Enums\DeviceBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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


    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
