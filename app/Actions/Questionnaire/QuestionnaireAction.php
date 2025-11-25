<?php

namespace App\Actions\Questionnaire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionOption;
use App\Models\QuestionAnswer;
use App\Http\Requests\Questionnaire\CreateQuestionnaireFormRequest;
use App\Http\Requests\Questionnaire\EditQuestionnaireFormRequest;

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
}
