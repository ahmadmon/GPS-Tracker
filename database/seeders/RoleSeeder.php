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
            ['title' => 'user', 'persian_name' => 'کاربر عادی', 'description' => 'کاربر ساده ای که میتواند از امکانات پایه سامانه استفاده کند.', 'created_at' => now()],
            ['title' => 'admin', 'persian_name' => 'ادمین', 'description' => 'کاربری که دسترسی های مدیریتی را داراست.', 'created_at' => now()],
            ['title' => 'super-admin', 'persian_name' => 'سوپر ادمین', 'description' => 'مدیر کلی که همه دسترسی ها برای اون باز است.', 'created_at' => now()],
            ['title' => 'manager', 'persian_name' => 'مدیر سازمان', 'description' => 'کاربر مدیرتی که دسترسی مدیریت سازمان های خود و همچنین زیرمجموعه های خود را دارد.', 'created_at' => now()],
            ['title' => 'developer', 'persian_name' => 'توسعه دهنده', 'description' => 'مدیر فنی سامانه که همه دسترسی ها را داراست.', 'created_at' => now()],
        ];

        DB::table('roles')->insert($roles);

        $superAdminRole = Role::where('title', 'super-admin')->first()?->id;
        $superAdminUser = User::where('user_type', 2)->first();
        $superAdminUser->roles()->sync([$superAdminRole]);
    }
}
