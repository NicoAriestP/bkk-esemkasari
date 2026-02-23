<?php

namespace App\Actions\Questionnaire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionOption;
use App\Models\QuestionAnswer;
use App\Models\QuestionnaireResponse;
use App\Enum\QuestionType;
use App\Http\Requests\Questionnaire\CreateQuestionnaireFormRequest;
use App\Http\Requests\Questionnaire\EditQuestionnaireFormRequest;
use App\Http\Requests\Questionnaire\SubmitQuestionnaireAnswersFormRequest;

class QuestionnaireAction
{
    public function save(CreateQuestionnaireFormRequest | EditQuestionnaireFormRequest $request, ?Questionnaire $model = null): Questionnaire
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Simpan atau perbarui model Questionnaire
            if ($model) {
                $model->update([
                    'title'       => $validated['title'],
                    'description' => $validated['description'],
                    'due_at'      => $validated['due_at'] ?? null,
                ]);
            } else {
                $model = Questionnaire::create([
                    'title'       => $validated['title'],
                    'description' => $validated['description'],
                    'due_at'      => $validated['due_at'] ?? null,
                ]);
            }

            // Hapus pertanyaan dan opsi yang ada jika memperbarui
            if ($model->questions()->exists()) {
                foreach ($model->questions as $question) {
                    $question->questionOptions()->delete();
                }
                $model->questions()->delete();
            }

            // Simpan pertanyaan dan opsi baru
            foreach ($validated['questions'] as $questionData) {
                $question = QuestionnaireQuestion::create([
                    'questionnaire_id' => $model->id,
                    'question_title'   => $questionData['question_title'],
                    'type'             => $questionData['type'],
                    'notes'            => $questionData['notes'] ?? null,
                ]);

                // Simpan opsi jika tipe pertanyaan adalah dropdown atau checkbox
                if (isset($questionData['options']) && is_array($questionData['options']) && count($questionData['options']) > 0) {
                    foreach ($questionData['options'] as $optionData) {
                        QuestionOption::create([
                            'question_id'  => $question->id,
                            'option_label' => $optionData['option_label'],
                        ]);
                    }
                }
            }

            DB::commit();

            return $model;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function submitAnswers(SubmitQuestionnaireAnswersFormRequest $request, Questionnaire $questionnaire, int $studentId): QuestionnaireResponse
    {
        // Validate that student hasn't already answered
        $existingResponse = $questionnaire->responses()
            ->where('student_id', $studentId)
            ->exists();

        if ($existingResponse) {
            throw new \Exception('Anda sudah mengisi kuesioner ini sebelumnya.');
        }

        // Validate deadline if exists
        if ($questionnaire->due_at && now()->isAfter($questionnaire->due_at)) {
            throw new \Exception('Batas waktu pengisian kuesioner telah berakhir.');
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Create questionnaire response
            $response = $questionnaire->responses()->create([
                'student_id' => $studentId,
                'submitted_at' => now(),
            ]);

            // Save each answer
            foreach ($validated['answers'] as $questionId => $answer) {
                $question = $questionnaire->questions()->find($questionId);

                if (!$question) {
                    continue;
                }

                // Handle different answer types
                if ($question->type->value === QuestionType::CHECKBOX->value && is_array($answer)) {
                    // For checkbox, create multiple question_answers
                    foreach ($answer as $selectedOption) {
                        $response->questionAnswers()->create([
                            'question_id' => $questionId,
                            'text_answer' => $selectedOption,
                        ]);
                    }
                } elseif ($question->type->value === QuestionType::DATE->value) {
                    // For date type, save to date_answer
                    $response->questionAnswers()->create([
                        'question_id' => $questionId,
                        'date_answer' => $answer,
                    ]);

                } else {
                    // For other types (dropdown, fillable), save to text_answer
                    $response->questionAnswers()->create([
                        'question_id' => $questionId,
                        'text_answer' => is_array($answer) ? json_encode($answer) : $answer,
                    ]);
                }
            }

            DB::commit();

            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
