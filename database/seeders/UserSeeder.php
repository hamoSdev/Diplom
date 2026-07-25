<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Create one known account per role for local development.
     * All accounts share the password "password".
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin', 'email' => 'admin@diplom.test', 'role' => UserRole::Admin],
            ['name' => 'Lecturer', 'email' => 'lecturer@diplom.test', 'role' => UserRole::Lecturer],
            ['name' => 'Student', 'email' => 'student@diplom.test', 'role' => UserRole::Student],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
