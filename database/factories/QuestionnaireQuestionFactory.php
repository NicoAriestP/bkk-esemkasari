<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionnaireQuestion>
 */
class QuestionnaireQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_id' => fake()->numberBetween(1, 10),
            'question' => fake()->sentence(3),
            'type' => fake()->randomElement(['options', 'fillable', 'date']),
            'notes' => fake()->paragraph(3),
            'is_multiple_answers' => fake()->boolean(),
        ];
    }
}
