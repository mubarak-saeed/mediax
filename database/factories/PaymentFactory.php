<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plan = Plan::factory()->create();
        $amount = $plan->price;

        return [
            'user_id' => User::factory(),
            'plan_id' => $plan,
            'amount' => $amount,
            'payment_method' => fake()->randomElement(['stripe', 'paypal', 'card']),
            'payment_status' => PaymentStatus::Completed,
            'transaction_id' => 'txn_'.fake()->unique()->uuid(),
            'paid_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatus::Pending,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatus::Failed,
        ]);
    }
}
