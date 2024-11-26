<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->delete();

        $roles = [
            ['title' => 'user', 'persian_name' => 'کاربر عادی', 'description' => 'کاربری است که به صورت محدود به برخی از امکانات پایه سامانه دسترسی دارد.', 'created_at' => now()],
            ['title' => 'admin', 'persian_name' => 'ادمین', 'description' => 'کاربری است که مسئولیت مدیریت کاربران، دستگاه ها و برخی از تنظیمات سامانه را بر عهده دارد.', 'created_at' => now()],
            ['title' => 'super-admin', 'persian_name' => 'سوپر ادمین', 'description' => 'دسترسی نامحدود به تمامی بخش‌های سامانه.', 'created_at' => now()],
            ['title' => 'manager', 'persian_name' => 'مدیر سازمان', 'description' => "کاربری است که مسئولیت مدیریت یک سازمان یا مجموعه مشخص از خودروها را بر عهده دارد.\nدسترسی کامل به اطلاعات و تنظیمات مربوط به سازمان خود و زیرمجموعه‌های آن.", 'created_at' => now()],
            ['title' => 'developer', 'persian_name' => 'توسعه دهنده', 'description' => 'کاربری است که مسئولیت توسعه و نگهداری سامانه را بر عهده دارد.', 'created_at' => now()],
        ];

        DB::table('roles')->insert($roles);

        $superAdminRole = Role::where('title', 'super-admin')->first()?->id;
        $superAdminUser = User::where('user_type', 2)->first();
        $superAdminUser->roles()->sync([$superAdminRole]);
    }
}
