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
            ['title' => 'create-device', 'persian_name' => 'ایجاد دستگاه', 'description' => '', 'created_at' => now()],
            ['title' => 'show-device', 'persian_name' => 'مشاهده دستگاه', 'description' => '', 'created_at' => now()],
            ['title' => 'edit-device', 'persian_name' => 'ویرایش دستگاه', 'description' => '', 'created_at' => now()],
            ['title' => 'delete-device', 'persian_name' => 'حذف دستگاه', 'description' => '', 'created_at' => now()],
            ['title' => 'create-user', 'persian_name' => 'ایجاد کاربر', 'description' => '', 'created_at' => now()],
            ['title' => 'show-user', 'persian_name' => 'مشاهده کاربر', 'description' => '', 'created_at' => now()],
            ['title' => 'edit-user', 'persian_name' => 'ویرایش کاربر', 'description' => '', 'created_at' => now()],
            ['title' => 'delete-user', 'persian_name' => 'حذف کاربر', 'description' => '', 'created_at' => now()],
            ['title' => 'create-geofence', 'persian_name' => 'ایجاد حصار جغرافیایی', 'description' => '', 'created_at' => now()],
            ['title' => 'show-geofence', 'persian_name' => 'مشاهده حصار جغرافیایی', 'description' => '', 'created_at' => now()],
            ['title' => 'edit-geofence', 'persian_name' => 'ویرایش حصار جغرافیایی', 'description' => '', 'created_at' => now()],
            ['title' => 'delete-geofence', 'persian_name' => 'حذف حصار جغرافیایی', 'description' => '', 'created_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
