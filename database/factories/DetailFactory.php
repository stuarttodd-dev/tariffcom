<?php

namespace Database\Factories;

use App\Models\Detail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Detail>
 */
class DetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $detailTypes = [
            'Full Name' => '👤',
            'Middle Initial' => '🔤',
            'Avatar' => '🖼️',
            'Gender' => '⚧',
            'Phone' => '📞',
            'Address' => '📍',
            'Company' => '🏢',
            'Position' => '💼',
        ];

        $key = fake()->randomElement(array_keys($detailTypes));
        $icon = $detailTypes[$key];

        return [
            'key' => $key,
            'value' => fake()->sentence(),
            'icon' => $icon,
            'status' => '1',
            'type' => 'detail',
            'user_id' => User::factory(),
        ];
    }

    /**
     * Create a detail with a specific key.
     */
    public function withKey(string $key): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
        ]);
    }

    /**
     * Create a detail with a specific value.
     */
    public function withValue(string $value): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $value,
        ]);
    }

    /**
     * Create a detail with a specific icon.
     */
    public function withIcon(string $icon): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }
}
