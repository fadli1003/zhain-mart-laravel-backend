<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'phone' => '081234567890',
        //     'photo' => 'djkaklsdasd',
        //     'password' => Hash::make('password'),
        // ]);
        Merchant::insert([
            'name' => 'zhain store',
            'address' => 'padang',
            'photo' => 'adklasldka',
            'phone' => '+630210939871',
            'keeper_id' => 1
        ]);
    }
}
