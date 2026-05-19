<?php

namespace App\Http\Controllers;

use App\Exports\Questionnaire\QuestionnaireResponsesExport;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Year;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class QuestionnaireResponseController extends Controller
{
    public function index(Questionnaire $model, Request $request)
    {
        $questionnaire = $model->load('questions.questionOptions');

        $selectedYearId = $request->integer('year_id');
        $selectedStudentClassId = $request->integer('student_class_id');

        $years = Year::query()
            ->select(['id', 'year'])
            ->with([
                'studentClasses' => fn ($query) => $query
                    ->select(['id', 'year_id', 'name'])
                    ->orderBy('name'),
            ])
            ->orderByDesc('year')
            ->get()
            ->map(function (Year $year) {
                return [
                    'id' => $year->id,
                    'year' => $year->year,
                    'studentClasses' => $year->studentClasses->map(function (StudentClass $studentClass) {
                        return [
                            'id' => $studentClass->id,
                            'year_id' => $studentClass->year_id,
                            'name' => $studentClass->name,
                        ];
                    })->values(),
                ];
            })
            ->values();

        $selectedYear = $selectedYearId
            ? $years->firstWhere('id', $selectedYearId)
            : null;

        $selectedStudentClass = null;
        $students = collect();

        if ($selectedYearId && $selectedStudentClassId) {
            $selectedStudentClass = StudentClass::query()
                ->select(['id', 'year_id', 'name'])
                ->where('year_id', $selectedYearId)
                ->find($selectedStudentClassId);

            if ($selectedStudentClass) {
                $students = Student::query()
                    ->select(['id', 'student_class_id', 'name', 'nisn'])
                    ->with([
                        'responses' => function ($query) use ($questionnaire) {
                            $query->select(['id', 'questionnaire_id', 'student_id', 'submitted_at'])
                                ->where('questionnaire_id', $questionnaire->id)
                                ->with([
                                    'questionAnswers' => fn ($answerQuery) => $answerQuery
                                        ->select(['id', 'response_id', 'question_id', 'question_option_id', 'text_answer', 'date_answer'])
                                        ->orderBy('id'),
                                ]);
                        },
                    ])
                    ->where('student_class_id', $selectedStudentClass->id)
                    ->orderBy('name')
                    ->get()
                    ->map(function (Student $student) {
                        $response = $student->responses->first();

                        return [
                            'id' => $student->id,
                            'name' => $student->name,
                            'nisn' => $student->nisn,
                            'response' => $response ? [
                                'id' => $response->id,
                                'submitted_at' => $response->submitted_at,
                            ] : null,
                        ];
                    })
                    ->values();
            }
        }

        return Inertia::render('questionnaire/responses/Index', [
            'questionnaire' => $questionnaire,
            'years' => $years,
            'students' => $students,
            'filters' => [
                'year_id' => $selectedYearId,
                'student_class_id' => $selectedStudentClassId,
            ],
            'selectedYear' => $selectedYear,
            'selectedStudentClass' => $selectedStudentClass,
            'totals' => [
                'total_students' => $students->count(),
                'responded_students' => $students->whereNotNull('response')->count(),
                'unanswered_students' => $students->whereNull('response')->count(),
            ],
        ]);
    }

    public function show(Questionnaire $model, Year $year, StudentClass $studentClass, Student $student)
    {
        if ($studentClass->year_id !== $year->id || $student->student_class_id !== $studentClass->id) {
            abort(404);
        }

        $questionnaire = $model->load('questions.questionOptions');

        $response = QuestionnaireResponse::query()
            ->with([
                'student.studentClass.year',
                'questionAnswers.questionOption',
            ])
            ->where('questionnaire_id', $questionnaire->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $normalizedResponse = $this->normalizeResponse($response);

        return Inertia::render('questionnaire/responses/Detail', [
            'questionnaire' => $questionnaire,
            'response' => $normalizedResponse,
        ]);
    }

    public function export(Questionnaire $model, Request $request)
    {
        $selectedYearId = $request->integer('year_id');
        $selectedStudentClassId = $request->integer('student_class_id');

        if (! $selectedYearId || ! $selectedStudentClassId) {
            return back()->with('error', 'Pilih angkatan dan kelas terlebih dahulu sebelum mengekspor respons.');
        }

        $year = Year::query()->select(['id', 'year'])->findOrFail($selectedYearId);
        $studentClass = StudentClass::query()
            ->select(['id', 'year_id', 'name'])
            ->where('year_id', $year->id)
            ->findOrFail($selectedStudentClassId);

        return Excel::download(
            new QuestionnaireResponsesExport($model, $year, $studentClass),
            "Export Respons Kuesioner - {$model->title} - {$studentClass->name} - {$year->year} - " . Carbon::now()->format('l, d-m-Y H-i') . ".xlsx"
        );
    }

    private function normalizeResponse(QuestionnaireResponse $response): array
    {
        $answers = $response->questionAnswers
            ->groupBy('question_id')
            ->map(function ($questionAnswers) {
                if ($questionAnswers->count() > 1) {
                    return $questionAnswers
                        ->pluck('question_option_id')
                        ->filter()
                        ->values()
                        ->all();
                }

                $answer = $questionAnswers->first();

                if ($answer->question_option_id !== null) {
                    return $answer->question_option_id;
                }

                if ($answer->date_answer !== null) {
                    return $answer->date_answer;
                }

                return $answer->text_answer ?? '';
            })
            ->toArray();

        return [
            'id' => $response->id,
            'submitted_at' => $response->submitted_at,
            'student' => [
                'id' => $response->student->id,
                'name' => $response->student->name,
                'nisn' => $response->student->nisn,
                'studentClass' => [
                    'id' => $response->student->studentClass->id,
                    'name' => $response->student->studentClass->name,
                    'year' => [
                        'id' => $response->student->studentClass->year->id,
                        'year' => $response->student->studentClass->year->year,
                    ],
                ],
            ],
            'answers' => $answers,
        ];
    }
}
