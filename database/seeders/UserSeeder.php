<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'username' => 'admin',
            'user_firstName' => 'System',
            'user_lastName' => 'Admin',
            'email_id' => 'admin@nirvana.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
        ]);

        $employee = User::create([
            'username' => 'employee1',
            'user_firstName' => 'John',
            'user_lastName' => 'Doe',
            'email_id' => 'emp@nirvana.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
        ]);

        $admin->roles()->attach(Role::where('name','Admin')->first());
        $employee->roles()->attach([
            Role::where('name','Employee')->first()->id,
            Role::where('name','Reviewer')->first()->id, // multiple roles ✔
        ]);
    }
}
