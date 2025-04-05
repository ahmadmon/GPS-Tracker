<?php

namespace App\Models;

use App\Enums\Wallet\TransactionStatus;
use App\Enums\Wallet\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    protected $guarded = ['id'];


    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }


    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
