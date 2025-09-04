<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Magazine>
 */
class MagazineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Technology', 'Finance', 'Health', 'Education', 'Entertainment', 'Sports', 'Travel', 'Food'];
        $languages = ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh'];
        
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'file_path' => 'magazines/' . $this->faker->uuid . '.pdf',
            'file_name' => $this->faker->word . '.pdf',
            'file_size' => $this->faker->numberBetween(1000000, 10000000), // 1MB to 10MB
            'mime_type' => 'application/pdf',
            'cover_image_path' => $this->faker->optional(0.7)->imageUrl(400, 600, 'magazine'),
            'category' => $this->faker->randomElement($categories),
            'language_code' => $this->faker->randomElement($languages),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'uploaded_by_admin_id' => Admin::factory(),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the magazine is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the magazine is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the magazine is in a specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Indicate that the magazine is in a specific language.
     */
    public function language(string $languageCode): static
    {
        return $this->state(fn (array $attributes) => [
            'language_code' => $languageCode,
        ]);
    }
}
