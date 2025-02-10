<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الأدوار
        $admin = Role::create(['name' => 'admin']);
        $customer = Role::create(['name' => 'customer']);


        // إدارة المنتجات والفئات
        Permission::create(['name' => 'manage products']);

        // إدارة الطلبات
        Permission::create(['name' => 'manage orders']);

        // إدارة المواعيد
        Permission::create(['name' => 'manage appointments']);

        // إدارة التقارير
        Permission::create(['name' => 'manage reports']);

        // إعطاء جميع الصلاحيات للمدير
        $admin->givePermissionTo(Permission::all());

        // إنشاء مستخدم مدير
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '01234567890',
            'address' => 'Cairo, Egypt'
        ]);

        // إعطاء دور المدير للمستخدم
        $adminUser->assignRole('admin');

        // إنشاء مستخدم عادي
        $customerUser = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '01234567891',
            'address' => 'Alexandria, Egypt'
        ]);

        // إعطاء دور العميل للمستخدم
        $customerUser->assignRole('customer');
    }
}
