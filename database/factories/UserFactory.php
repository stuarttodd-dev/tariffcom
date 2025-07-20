<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefixes = ['Mr', 'Mrs', 'Ms'];
        
        return [
            'prefixname' => fake()->randomElement($prefixes),
            'firstname' => fake()->firstName(),
            'middlename' => fake()->optional()->firstName(),
            'lastname' => fake()->lastName(),
            'suffixname' => fake()->optional()->suffix(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'photo' => fake()->optional()->imageUrl(),
            'type' => fake()->randomElement(['user', 'admin']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'admin',
        ]);
    }

    /**
     * Create a regular user.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'user',
        ]);
    }

    /**
     * Create a user with a specific prefix.
     */
    public function withPrefix(string $prefix): static
    {
        return $this->state(fn (array $attributes) => [
            'prefixname' => $prefix,
        ]);
    }
}
