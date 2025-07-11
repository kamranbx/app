<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@manager.com',
            'password' => Hash::make('password'),
            'role' => UserRole::MANAGER,
        ]);

        User::create([
            'name' => 'Normal User',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
        ]);
    }
}
