<?php

namespace App\Models;

use App\Enums\Wallet\TransactionStatus;
use App\Enums\Wallet\TransactionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    protected $guarded = ['id'];


    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class
    ];

    protected function statusDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match (true) {
                    $this->status->isSuccessful() && $this->type->isCredit() => [
                        'label' => 'واریز موفق',
                        'color' => 'success',
                    ],
                    $this->status->isSuccessful() && $this->type->isDebit() => [
                        'label' => 'برداشت موفق',
                        'color' => 'success',
                    ],
                    $this->status->isPending() => [
                        'label' => 'در انتظار پرداخت',
                        'color' => 'warning',
                    ],
                    $this->status->isFailed() => [
                        'label' => 'ناموفق یا لغو شده',
                        'color' => 'dark',
                    ],
                    default => [
                        'label' => '-',
                        'color' => 'secondary',
                    ],
                };
            }
        );
    }


    protected function typeDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                $isSuccessful = $this->status->isSuccessful();
                $isCredit = $this->type->isCredit();

                return [
                    'label' => $this->type->label(),
                    'color' => $isSuccessful
                        ? ($isCredit ? 'success' : 'danger')
                        : 'dark',
                    'icon' => $isSuccessful
                        ? ($isCredit ? 'plus-circle' : 'minus-circle')
                        : 'x-circle',
                ];
            }
        );
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }


    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
