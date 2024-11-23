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
        DB::table('permissions')->delete();

        $permissions = [
            // Devices
            //------------------------------------------
            ['title' => 'devices-list', 'persian_name' => 'مشاهده لیست دستگاه‌ ها', 'group' => 'devices', 'created_at' => now()],
            ['title' => 'show-device', 'persian_name' => 'مشاهده جزئیات دستگاه', 'group' => 'devices', 'created_at' => now()],
            ['title' => 'create-device', 'persian_name' => 'ایجاد دستگاه', 'group' => 'devices', 'created_at' => now()],
            ['title' => 'edit-device', 'persian_name' => 'ویرایش دستگاه', 'group' => 'devices', 'created_at' => now()],
            ['title' => 'delete-device', 'persian_name' => 'حذف دستگاه', 'group' => 'devices', 'created_at' => now()],
            ['title' => 'device-settings', 'persian_name' => 'تنظیمات دستگاه', 'group' => 'devices', 'created_at' => now()],
            ['title' => 'device-location', 'persian_name' => 'موقعیت دستگاه', 'group' => 'devices', 'created_at' => now()], //*
            // Vehicles
            //------------------------------------------
            ['title' => 'vehicles-list', 'persian_name' => 'مشاهده لیست وسایل نقلیه', 'group' => 'vehicles', 'created_at' => now()],
            ['title' => 'show-vehicle', 'persian_name' => 'مشاهده جزئیات وسیله نقلیه', 'group' => 'vehicles', 'created_at' => now()],
            ['title' => 'create-vehicle', 'persian_name' => 'ایجاد وسایل نقلیه', 'group' => 'vehicles', 'created_at' => now()],
            ['title' => 'edit-vehicle', 'persian_name' => 'ویرایش وسایل نقلیه', 'group' => 'vehicles', 'created_at' => now()],
            ['title' => 'delete-vehicle', 'persian_name' => 'حذف وسایل نقلیه', 'group' => 'vehicles', 'created_at' => now()],
            ['title' => 'vehicle-location', 'persian_name' => 'موقعیت وسایل نقلیه', 'group' => 'vehicles', 'created_at' => now()],
            // Users
            //------------------------------------------
            ['title' => 'users-list', 'persian_name' => 'مشاهده لیست کاربران', 'group' => 'users', 'created_at' => now()],
            ['title' => 'show-user', 'persian_name' => 'مشاهده جزئیات کاربر', 'group' => 'users', 'created_at' => now()],
            ['title' => 'create-user', 'persian_name' => 'ایجاد کاربر', 'group' => 'users', 'created_at' => now()],
            ['title' => 'edit-user', 'persian_name' => 'ویرایش کاربر', 'group' => 'users', 'created_at' => now()],
            ['title' => 'delete-user', 'persian_name' => 'حذف کاربر', 'group' => 'users', 'created_at' => now()],
            ['title' => 'user-permissions', 'persian_name' => 'مدیریت دسترسی‌ها', 'group' => 'users', 'created_at' => now()],
            // Companies
            //------------------------------------------
            ['title' => 'companies-list', 'persian_name' => 'مشاهده لیست سازمان ها', 'group' => 'companies', 'created_at' => now()],
            ['title' => 'show-company', 'persian_name' => 'مشاهده جزئیات سازمان', 'group' => 'companies', 'created_at' => now()],
            ['title' => 'create-company', 'persian_name' => 'ایجاد سازمان', 'group' => 'companies', 'created_at' => now()],
            ['title' => 'edit-company', 'persian_name' => 'ویرایش سازمان', 'group' => 'companies', 'created_at' => now()],
            ['title' => 'delete-company', 'persian_name' => 'حذف سازمان', 'group' => 'companies', 'created_at' => now()],
            ['title' => 'manage-subsets', 'persian_name' => 'مدیریت زیر مجموعه ها (افزودن و حذف)', 'group' => 'companies', 'created_at' => now()],
            // Geofences
            //------------------------------------------
            ['title' => 'geofences-list', 'persian_name' => 'مشاهده لیست حصارها', 'group' => 'geofences', 'created_at' => now()],
            ['title' => 'show-geofence', 'persian_name' => 'مشاهده جزئیات حصار', 'group' => 'geofences', 'created_at' => now()],
            ['title' => 'create-geofence', 'persian_name' => 'ایجاد حصار', 'group' => 'geofences', 'created_at' => now()],
            ['title' => 'edit-geofence', 'persian_name' => 'ویرایش حصار', 'group' => 'geofences', 'created_at' => now()],
            ['title' => 'delete-geofence', 'persian_name' => 'حذف حصار', 'group' => 'geofences', 'created_at' => now()],
            // Map
            //------------------------------------------
            ['title' => 'show-map', 'persian_name' => 'مشاهده نقشه', 'group' => 'map', 'created_at' => now()],
            // Site Settings
            //------------------------------------------
            ['title' => 'site-settings', 'persian_name' => 'تنظیمات سامانه', 'group' => 'site-settings', 'created_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
