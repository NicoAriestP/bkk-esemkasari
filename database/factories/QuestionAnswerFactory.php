<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionAnswer>
 */
class QuestionAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $question = \App\Models\QuestionnaireQuestion::inRandomOrder()->first() ?? \Database\Factories\QuestionnaireQuestionFactory::new()->create();
        $isOption = $question->type === 'options';
        return [
            'question_id' => $question->id,
            'question_option_id' => $isOption ? (\App\Models\QuestionOption::where('question_id', $question->id)->inRandomOrder()->first()?->id ?? null) : null,
            'text_answer' => $isOption ? null : fake()->paragraph(3),
            'is_selected' => $isOption ? fake()->boolean() : 0,
        ];
    }
}
