<?php

namespace Database\Seeders;

use App\Models\Permission;
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
        $roles = [
            ['title' => 'user', 'persian_name' => 'کاربر عادی', 'description' => 'کاربری است که به صورت محدود به برخی از امکانات پایه سامانه دسترسی دارد.', 'created_at' => now()],
            ['title' => 'admin', 'persian_name' => 'ادمین', 'description' => 'کاربری است که مسئولیت مدیریت کاربران، دستگاه ها و برخی از تنظیمات سامانه را بر عهده دارد.', 'created_at' => now()],
            ['title' => 'super-admin', 'persian_name' => 'سوپر ادمین', 'description' => 'دسترسی نامحدود به تمامی بخش‌های سامانه.', 'created_at' => now()],
            ['title' => 'manager', 'persian_name' => 'مدیر سازمان', 'description' => "کاربری است که مسئولیت مدیریت یک سازمان یا مجموعه مشخص از خودروها را بر عهده دارد.\nدسترسی کامل به اطلاعات و تنظیمات مربوط به سازمان خود و زیرمجموعه‌های آن.", 'created_at' => now()],
            ['title' => 'developer', 'persian_name' => 'توسعه دهنده', 'description' => 'کاربری است که مسئولیت توسعه و نگهداری سامانه را بر عهده دارد.', 'created_at' => now()],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['title' => $role['title']],
                array_merge($role, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // Super admin Role's permissions
        $superAdminRole = Role::where('title', 'super-admin')->first();

        if ($superAdminRole) {
            $superAdminRole->permissions()->sync(Permission::pluck('id'));

        }

        // admin Role's permissions
        $adminRole = Role::where('title', 'admin')->first();
        if ($adminRole) {
            $adminPermissionGroups = ['wallet', 'subscriptions', 'subscription-plans'];
            $permissions = Permission::whereIn('group', $adminPermissionGroups)->pluck('id');

            $adminRole->permissions()->syncWithoutDetaching($permissions);
        }

    }
}
