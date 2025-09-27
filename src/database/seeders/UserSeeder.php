<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 管理者
        User::create([
            'username' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'profile_image' => null,
            'zipcode' => null,
            'address' => null,
            'building' => null,
        ]);

        // 一般ユーザー
        User::create([
            'username' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'profile_image' => null,
            'zipcode' => null,
            'address' => null,
            'building' => null,
        ]);

        $users = [
            [
                'username' => 'yamada',
                'email' => 'yamada@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => null,
                'zipcode' => '1000001',
                'address' => '東京都千代田区千代田1-1',
                'building' => '皇居前ビル101'
            ],
            [
                'username' => 'suzuki',
                'email' => 'suzuki@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => null,
                'zipcode' => '1500002',
                'address' => '東京都渋谷区渋谷2-2-2',
                'building' => '渋谷ハイツ201'
            ],
            [
                'username' => 'tanaka',
                'email' => 'tanaka@example.com',
                'password' => Hash::make('password123'),
                'profile_image' => null,
                'zipcode' => null,
                'address' => null,
                'building' => null
            ],

        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
