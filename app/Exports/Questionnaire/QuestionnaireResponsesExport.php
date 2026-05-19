<?php

namespace App\Exports\Questionnaire;

use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\StudentClass;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class QuestionnaireResponsesExport implements FromView
{
    use Exportable;

    public function __construct(
        private Questionnaire $questionnaire,
        private Year $year,
        private StudentClass $studentClass,
    ) {
    }

    public function view(): View
    {
        $questionnaire = $this->questionnaire->load('questions.questionOptions');

        $responses = QuestionnaireResponse::query()
            ->with([
                'student.studentClass.year',
                'questionAnswers.questionOption',
            ])
            ->where('questionnaire_id', $questionnaire->id)
            ->whereHas('student', function ($query) {
                $query->where('student_class_id', $this->studentClass->id);
            })
            ->orderBy('submitted_at')
            ->get()
            ->map(function (QuestionnaireResponse $response) {
                $answers = $response->questionAnswers
                    ->groupBy('question_id')
                    ->map(function ($questionAnswers) {
                        if ($questionAnswers->count() > 1) {
                            return $questionAnswers
                                ->pluck('questionOption.option_label')
                                ->filter()
                                ->values()
                                ->implode(', ');
                        }

                        $answer = $questionAnswers->first();

                        if ($answer->date_answer !== null) {
                            return Carbon::parse($answer->date_answer)->translatedFormat('d F Y');
                        }

                        if ($answer->text_answer !== null && $answer->text_answer !== '') {
                            return $answer->text_answer;
                        }

                        return $answer->questionOption?->option_label ?? '-';
                    })
                    ->toArray();

                return [
                    'student' => $response->student->name,
                    'nisn' => $response->student->nisn,
                    'submitted_at' => Carbon::parse($response->submitted_at)
                        ->locale('id')
                        ->translatedFormat('l, d F Y H:i'),
                    'answers' => $answers,
                ];
            });

        return view('exports.questionnaire.responses', [
            'questionnaire' => $questionnaire,
            'year' => $this->year,
            'studentClass' => $this->studentClass->load('year'),
            'responses' => $responses,
        ]);
    }
}
