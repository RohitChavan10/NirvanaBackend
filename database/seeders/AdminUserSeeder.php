<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
    {
        User::create([
            'username'       => 'admin',
            'user_firstName' => 'System',
            'user_lastName'  => 'Admin',
            'email_id'       => 'admin@gmail.com',
            'password'       => Hash::make('password'),  // Change as needed
            'user_type'      => 'admin',
        ]);
    }
}
