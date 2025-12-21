<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['code' => 'BUILDING', 'name' => 'Building Management'],
            ['code' => 'LEASE', 'name' => 'Lease Management'],
            ['code' => 'EXPENSE', 'name' => 'Expense Management'],
            ['code' => 'WORKFLOW', 'name' => 'Workflow Management'],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
