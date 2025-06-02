<?php

namespace App\Models;

use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasUserType($type): bool
    {
        $userType = match ($type) {
            'user' => 0,
            'admin' => 1,
            'super-admin' => 2,
            'manager' => 3
        };

        return $this->user_type == $userType;
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return match ((int)$this->user_type) {
                    0 => ['color' => 'danger', 'name' => 'کاربر'],
                    1 => ['color' => 'success', 'name' => 'ادمین'],
                    2 => ['color' => 'warning', 'name' => 'سوپر ادمین'],
                    3 => ['color' => 'primary', 'name' => 'مدیر سازمان'],
                    default => ['color' => '', 'name' => '']
                };
            }
        );
    }

    /**
     * @return void
     *
     */
    protected static function booted(): void
    {
        static::created(fn($user) => $user->wallet()->create());
    }

    protected function joinedCompaniesList(): Attribute
    {
        return Attribute::make(
            get: fn(): array|string => $this->joinedCompanies->pluck('name')->implode(', ')
        );
    }


    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'user_id')->where('status', 1);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'user_id');
    }

    /**
     * @return Collection
     *
     * related to The users Company (Joined by Company)
     */
    public function subsets(): Collection
    {
        if (!$this->hasUserType('manager')) {
            return collect([]);
        }

        return $this->companies->flatMap(fn($company) => $company->users);
    }

    public function joinedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'companies_user');
    }

    /**
     * @return HasMany
     *
     * related to The Company manager
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'user_id')->where('status', 1);
    }

    public function geofences(): HasManyThrough
    {
        return $this->hasManyThrough(Geofence::class, Device::class);
    }

    /**
     * Get the user's wallet.
     */
    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'walletable');
    }


    /**
     * @return bool
     */
    public function isSubscriber(): bool
    {
        return $this->wallet->hasSubscription();
    }


}
