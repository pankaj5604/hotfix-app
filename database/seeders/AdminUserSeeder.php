<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['mobile' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'mobile' => '8238823001',
                'password' => Hash::make('password'), // 🔹 default password
                'is_admin' => true, // optional column if you have roles
            ]
        );
    }
}
