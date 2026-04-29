<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@fintrack.app'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('demo1234'),
                'locale'   => 'ru',
                'currency' => 'KZT',
            ]
        );
    }
}
