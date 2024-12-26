<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        Permission::create(['name' => 'manage-tasks']);
        Permission::create(['name' => 'view-tasks']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(['manage-tasks', 'view-tasks']);
        $userRole->givePermissionTo('view-tasks');
    }
}
