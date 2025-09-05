<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version' => $this->faker->unique()->semver(),
            'language' => $this->faker->randomElement(['en', 'es']),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'is_active' => true,
            'effective_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Create a contract in a specific language.
     */
    public function inLanguage(string $language): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => $language,
        ]);
    }

    /**
     * Create an active contract.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive contract.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a contract with a specific effective date.
     */
    public function effectiveFrom(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'effective_date' => $date,
        ]);
    }
}

