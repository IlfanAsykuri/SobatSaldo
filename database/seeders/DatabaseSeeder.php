<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed categories & global keywords dulu
        $this->call(CategorySeeder::class);

        // Buat user demo
        $user = User::create([
            'name'              => 'Ilfan Asykuri',
            'email'             => 'demo@sobatsaldo.id',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(), // langsung verified untuk demo
        ]);

        // Buat wallet demo
        Wallet::create(['user_id' => $user->id, 'name' => 'BCA',     'type' => 'bank',    'color_theme' => 'blue',    'account_number' => '8801 2345 6789']);
        Wallet::create(['user_id' => $user->id, 'name' => 'Mandiri', 'type' => 'bank',    'color_theme' => 'amber',   'account_number' => '1230 0987 6543']);
        Wallet::create(['user_id' => $user->id, 'name' => 'Cash',    'type' => 'cash',    'color_theme' => 'emerald', 'account_number' => null]);
    }
}
