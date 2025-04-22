<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(3),
            'due_at' => fake()->dateTimeBetween('+1 week', '+2 week'),
            'gender' => fake()->randomElement(['laki-laki', 'perempuan']),
            'location' => fake()->address(),
            'file' => fake()->imageUrl(640, 480),
            'created_by' => fake()->unique()->numberBetween(1, 10),
        ];
    }
}
