<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Questionnaire>
 */
class QuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            // 'type' => fake()->randomElement([
            //     'questionnaire',
            //     'detail_activity',
            //     'feedback',
            //     'student_activity',
            //     'student_working'
            // ]),
            'due_at' => fake()->dateTimeBetween('+1 week', '+2 week'),
        ];
    }
}
