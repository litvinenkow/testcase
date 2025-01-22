<?php

namespace Database\Seeders;

use App\Models\Promocode;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => 'test'
        ]);
        Promocode::create([
            'promocode' => 'TEST_1_1',
            'use_count' => 1,
            'use_max' => 1,
            'amount' => 10
        ]);
        Promocode::create([
            'promocode' => 'TEST_2_1',
            'use_count' => 2,
            'use_max' => 1,
            'amount' => 10
        ]);
        Promocode::create([
            'promocode' => 'INVALID_DATE',
            'use_count' => 1,
            'use_max' => 1,
            'amount' => 10,
            'valid_till' => now()->subDay()
        ]);
        Promocode::create([
            'promocode' => 'VALID_DATE',
            'use_count' => 10,
            'use_max' => 5,
            'amount' => 10,
            'valid_till' => now()->addDays(100)
        ]);
        Promocode::create([
            'promocode' => 'UNLIMITED',
            'amount' => 10,
        ]);
    }
}
