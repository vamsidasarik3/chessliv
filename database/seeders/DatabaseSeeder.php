<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@chessliv.com'],
            [
                'name' => 'ChessLiv Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->call([
            ChesslivSeeder::class,
            ArticleGameSeeder::class,
        ]);
    }
}
