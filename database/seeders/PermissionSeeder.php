<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            'create',
            'view',
            'edit',
            'delete',
            'submit',
            'review',
            'approve',
            'reject',
        ];

        foreach ($actions as $action) {
            Permission::create([
                'action' => $action,
            ]);
        }
    }
}
