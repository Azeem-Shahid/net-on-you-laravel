<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Referral::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'referred_user_id' => User::factory(),
            'level' => $this->faker->numberBetween(1, 6),
        ];
    }

    /**
     * Create a referral at a specific level.
     */
    public function atLevel(int $level): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => $level,
        ]);
    }

    /**
     * Create a referral between specific users.
     */
    public function betweenUsers(int $referrerId, int $referredId, int $level = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $referrerId,
            'referred_user_id' => $referredId,
            'level' => $level,
        ]);
    }
}

