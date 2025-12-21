<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleModulePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'Admin')->first();
        $manager = Role::where('name', 'Manager')->first();
        $reviewer = Role::where('name', 'Reviewer')->first();
        $employee = Role::where('name', 'Employee')->first();

        $modules = Module::all();
        $permissions = Permission::all();

        // Admin → full access
        foreach ($modules as $module) {
            foreach ($permissions as $permission) {
                DB::table('role_module_permissions')->insert([
                    'role_id' => $admin->id,
                    'module_id' => $module->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Employee
        $this->attach($employee, 'BUILDING', ['create','view','submit']);
        $this->attach($employee, 'LEASE', ['create','view','submit']);
        $this->attach($employee, 'EXPENSE', ['create','view']);

        // Reviewer
        $this->attach($reviewer, 'BUILDING', ['view','review','reject']);
        $this->attach($reviewer, 'LEASE', ['view','review']);

        // Manager
        $this->attach($manager, 'BUILDING', ['view','approve']);
        $this->attach($manager, 'LEASE', ['view','approve']);
        $this->attach($manager, 'EXPENSE', ['approve']);
    }

    private function attach($role, $moduleCode, $permissionSlugs)
    {
        $module = Module::where('code', $moduleCode)->first();

        foreach ($permissionSlugs as $slug) {
            $permission = Permission::where('action', $slug)->first();

            DB::table('role_module_permissions')->insert([
                'role_id' => $role->id,
                'module_id' => $module->id,
                'permission_id' => $permission->id,
            ]);
        }
    }
}
