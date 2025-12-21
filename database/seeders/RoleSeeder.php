<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'code' => 'ADMIN'],
            ['name' => 'Manager', 'code' => 'MANAGER'],
            ['name' => 'Reviewer', 'code' => 'REVIEWER'],
            ['name' => 'Employee', 'code' => 'EMPLOYEE'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['code' => $role['code']],
                ['name' => $role['name']]
            );
        }
    }
}
