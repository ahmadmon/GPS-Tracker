<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            // Devices
            //------------------------------------------
            ['title' => 'devices-list', 'persian_name' => 'مشاهده لیست دستگاه‌ ها', 'group' => 'devices', 'order' => 1, 'created_at' => now()],
            ['title' => 'show-device', 'persian_name' => 'مشاهده جزئیات دستگاه', 'group' => 'devices', 'order' => 1, 'created_at' => now()],
            ['title' => 'create-device', 'persian_name' => 'ایجاد دستگاه', 'group' => 'devices', 'order' => 1, 'created_at' => now()],
            ['title' => 'edit-device', 'persian_name' => 'ویرایش دستگاه', 'group' => 'devices', 'order' => 1, 'created_at' => now()],
            ['title' => 'delete-device', 'persian_name' => 'حذف دستگاه', 'group' => 'devices', 'order' => 1, 'created_at' => now()],
            ['title' => 'device-settings', 'persian_name' => 'تنظیمات دستگاه', 'group' => 'devices', 'order' => 1, 'created_at' => now()],
            ['title' => 'device-location', 'persian_name' => 'موقعیت دستگاه', 'group' => 'devices', 'order' => 1, 'created_at' => now()],

            // Vehicles
            //------------------------------------------
            ['title' => 'vehicles-list', 'persian_name' => 'مشاهده لیست وسایل نقلیه', 'group' => 'vehicles', 'order' => 2, 'created_at' => now()],
            ['title' => 'show-vehicle', 'persian_name' => 'مشاهده جزئیات وسیله نقلیه', 'group' => 'vehicles', 'order' => 2, 'created_at' => now()],
            ['title' => 'create-vehicle', 'persian_name' => 'ایجاد وسایل نقلیه', 'group' => 'vehicles', 'order' => 2, 'created_at' => now()],
            ['title' => 'edit-vehicle', 'persian_name' => 'ویرایش وسایل نقلیه', 'group' => 'vehicles', 'order' => 2, 'created_at' => now()],
            ['title' => 'delete-vehicle', 'persian_name' => 'حذف وسایل نقلیه', 'group' => 'vehicles', 'order' => 2, 'created_at' => now()],
            ['title' => 'vehicle-location', 'persian_name' => 'موقعیت وسایل نقلیه', 'group' => 'vehicles', 'order' => 2, 'created_at' => now()],

            // Users
            //------------------------------------------
            ['title' => 'users-list', 'persian_name' => 'مشاهده لیست کاربران', 'group' => 'users', 'order' => 3, 'created_at' => now()],
            ['title' => 'show-user', 'persian_name' => 'مشاهده جزئیات کاربر', 'group' => 'users', 'order' => 3, 'created_at' => now()],
            ['title' => 'create-user', 'persian_name' => 'ایجاد کاربر', 'group' => 'users', 'order' => 3, 'created_at' => now()],
            ['title' => 'edit-user', 'persian_name' => 'ویرایش کاربر', 'group' => 'users', 'order' => 3, 'created_at' => now()],
            ['title' => 'delete-user', 'persian_name' => 'حذف کاربر', 'group' => 'users', 'order' => 3, 'created_at' => now()],
            ['title' => 'user-permissions', 'persian_name' => 'مدیریت دسترسی‌ها', 'group' => 'users', 'order' => 3, 'created_at' => now()],

            // Companies
            //------------------------------------------
            ['title' => 'companies-list', 'persian_name' => 'مشاهده لیست سازمان ها', 'group' => 'companies', 'order' => 4, 'created_at' => now()],
            ['title' => 'show-company', 'persian_name' => 'مشاهده جزئیات سازمان', 'group' => 'companies', 'order' => 4, 'created_at' => now()],
            ['title' => 'create-company', 'persian_name' => 'ایجاد سازمان', 'group' => 'companies', 'order' => 4, 'created_at' => now()],
            ['title' => 'edit-company', 'persian_name' => 'ویرایش سازمان', 'group' => 'companies', 'order' => 4, 'created_at' => now()],
            ['title' => 'delete-company', 'persian_name' => 'حذف سازمان', 'group' => 'companies', 'order' => 4, 'created_at' => now()],
            ['title' => 'manage-subsets', 'persian_name' => 'مدیریت زیر مجموعه ها (افزودن و حذف)', 'group' => 'companies', 'order' => 4, 'created_at' => now()],

            // Geofences
            //------------------------------------------
            ['title' => 'geofences-list', 'persian_name' => 'مشاهده لیست حصارها', 'group' => 'geofences', 'order' => 5, 'created_at' => now()],
            ['title' => 'show-geofence', 'persian_name' => 'مشاهده جزئیات حصار', 'group' => 'geofences', 'order' => 5, 'created_at' => now()],
            ['title' => 'create-geofence', 'persian_name' => 'ایجاد حصار', 'group' => 'geofences', 'order' => 5, 'created_at' => now()],
            ['title' => 'edit-geofence', 'persian_name' => 'ویرایش حصار', 'group' => 'geofences', 'order' => 5, 'created_at' => now()],
            ['title' => 'delete-geofence', 'persian_name' => 'حذف حصار', 'group' => 'geofences', 'order' => 5, 'created_at' => now()],

            // Wallet
            //------------------------------------------
            ['title' => 'wallet-list', 'persian_name' => 'مشاهده موجودی', 'group' => 'wallet', 'order' => 6, 'created_at' => now()],
            ['title' => 'show-wallet', 'persian_name' => 'مشاهده جزئیات', 'group' => 'wallet', 'order' => 6, 'created_at' => now()],
            ['title' => 'credit-wallet', 'persian_name' => 'افزایش موجودی (واریز)', 'group' => 'wallet', 'order' => 6, 'created_at' => now()],
            ['title' => 'debit-wallet', 'persian_name' => 'کاهش موجودی (برداشت)', 'group' => 'wallet', 'order' => 6, 'created_at' => now()],
            ['title' => 'wallet-pay-gateway', 'persian_name' => 'پرداخت اینترنتی (درگاه)', 'group' => 'wallet', 'order' => 6, 'created_at' => now()],
            ['title' => 'wallet-manual-credit', 'persian_name' => 'واریز دستی', 'group' => 'wallet', 'order' => 6, 'created_at' => now()],

            // Subscription Plans
            //------------------------------------------
            ['title' => 'plan-list', 'persian_name' => 'مشاهده لیست پلن ها', 'group' => 'subscription-plans', 'order' => 7, 'created_at' => now()],
            ['title' => 'show-plan', 'persian_name' => 'مشاهده جزئیات پلن', 'group' => 'subscription-plans', 'order' => 7, 'created_at' => now()],
            ['title' => 'create-plan', 'persian_name' => 'ایجاد پلن', 'group' => 'subscription-plans', 'order' => 7, 'created_at' => now()],
            ['title' => 'edit-plan', 'persian_name' => 'ویرایش پلن', 'group' => 'subscription-plans', 'order' => 7, 'created_at' => now()],
            ['title' => 'delete-plan', 'persian_name' => 'حذف پلن', 'group' => 'subscription-plans', 'order' => 7, 'created_at' => now()],

            // Subscriptions
            //------------------------------------------
            ['title' => 'user-subscriptions-list', 'persian_name' => 'مشاهده اشتراک های کاربران', 'group' => 'subscriptions', 'order' => 8, 'created_at' => now()],
            ['title' => 'company-subscriptions-list', 'persian_name' => 'مشاهده اشتراک های سازمان‌ها', 'group' => 'subscriptions', 'order' => 8, 'created_at' => now()],
            ['title' => 'create-user-subscription', 'persian_name' => 'اعطای اشتراک به کاربر', 'group' => 'subscriptions', 'order' => 8, 'created_at' => now()],
            ['title' => 'create-company-subscription', 'persian_name' => 'اعطای اشتراک به سازمان', 'group' => 'subscriptions', 'order' => 8, 'created_at' => now()],
            ['title' => 'revoke-user-subscription', 'persian_name' => 'لغو اشتراک کاربر', 'group' => 'subscriptions', 'order' => 8, 'created_at' => now()],

            // Map
            //------------------------------------------
            ['title' => 'show-map', 'persian_name' => 'مشاهده نقشه', 'group' => 'map', 'order' => 9, 'created_at' => now()],

            // Site Settings
            //------------------------------------------
            ['title' => 'site-settings', 'persian_name' => 'تنظیمات سامانه', 'group' => 'site-settings', 'order' => 10, 'created_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(['title' => $permission['title']],
                array_merge($permission, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
