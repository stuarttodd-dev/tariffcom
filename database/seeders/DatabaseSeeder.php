<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'type' => 'admin'
        ]);

        User::factory(5)->create();
    }
}
