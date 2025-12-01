<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Inertia\Inertia;

class StudentQuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $questionnaires = Questionnaire::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%$search%");
            })
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('questionnaire/student/List', [
            'models' => $questionnaires,
        ]);
    }

    public function show(Questionnaire $model)
    {
        $model->load('questionnaireQuestions.questionOptions');

        return Inertia::render('questionnaire/student/Show', [
            'model' => $model,
        ]);
    }
}
