<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $duration = $this->faker->randomElement([30, 90, 365]); // days
        
        return [
            'user_id' => User::factory(),
            'plan_name' => $this->faker->randomElement(['monthly', 'quarterly', 'annual']),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => (clone $startDate)->modify("+{$duration} days")->format('Y-m-d'),
            'status' => $this->faker->randomElement(['active', 'expired', 'cancelled']),
            'last_renewed_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the subscription is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'end_date' => now()->addDays(rand(1, 365))->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the subscription is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'end_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the subscription is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'end_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the subscription is monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_name' => 'monthly',
            'end_date' => now()->addDays(30)->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the subscription is annual.
     */
    public function annual(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_name' => 'annual',
            'end_date' => now()->addDays(365)->format('Y-m-d'),
        ]);
    }
}
