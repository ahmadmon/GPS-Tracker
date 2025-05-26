<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $guarded = ['id'];


    protected function groupName(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return match ($this->group) {
                    'devices' => 'بخش دستگاه ها',
                    'vehicles' => 'بخش وسایل نقلیه',
                    'users' => 'بخش کاربران',
                    'companies' => 'بخش سازمان ها',
                    'geofences' => 'بخش حصار ‌ها',
                    'wallet' => 'بخش کیف پول',
                    'subscription-plans' => 'بخش طرح های اشتراک',
                    'subscriptions' => 'بخش اشتراک ها',
                    'map' => 'بخش نقشه',
                    'site-settings' => 'بخش تنظیمات سایت',
                    default => '',
                };
            }
        );
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }
}
