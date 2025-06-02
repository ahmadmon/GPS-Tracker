<?php

namespace App\Models;

use App\Enums\Wallet\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }
}
