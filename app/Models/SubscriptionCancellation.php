<?php

namespace App\Models;

use App\Enums\Subscription\CancellationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionCancellation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => CancellationStatus::class
    ];


    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
