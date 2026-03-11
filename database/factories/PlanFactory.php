<?php

namespace Database\Factories;

use App\Enums\AccessLevel;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['الباقة الأساسية', 'الباقة الذهبية', 'الباقة البرونزية']),
            'price' => fake()->randomFloat(2, 9.99, 49.99),
            'description' => fake()->sentence(),
            'access_level' => fake()->randomElement(AccessLevel::cases()),
        ];
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'مجاني',
            'price' => 0,
            'access_level' => AccessLevel::Free,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => AccessLevel::Premium,
        ]);
    }
}
