<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\StudentActivityAnswer;
use App\Models\StudentUniversityAnswer;
use App\Models\StudentWorkingAnswer;
use App\Models\FeedbackAnswer;
use App\Models\DetailActivityAnswer;
use App\Models\StudentEntrepreneurAnswer;
use App\Http\Requests\TracerStudy\SaveTracerStudyFormRequest;
use App\Actions\TracerStudy\TracerStudyAction;

class TracerStudyController extends Controller
{
    private function getStudentData()
    {
        $student = Auth::user();

        return [
            'student' => $student,
            'studentActivityAnswer' => $student->studentActivityAnswer,
            'studentUniversityAnswer' => $student->studentUniversityAnswer,
            'studentWorkingAnswer' => $student->studentWorkingAnswer,
            'feedbackAnswer' => $student->feedbackAnswer,
            'detailActivityAnswer' => $student->detailActivityAnswer,
            'studentEntrepreneurAnswer' => $student->studentEntrepreneurAnswer,
        ];
    }

    public function tracerStudy()
    {
        return Inertia::render('tracerStudy/TracerStudy', $this->getStudentData());
    }

    public function saveTracerStudy(TracerStudyAction $tracerStudyAction, SaveTracerStudyFormRequest $request)
    {
        $tracerStudy = $tracerStudyAction->save($this->getStudentData()['student'], $request);

        return redirect()->route('tracer-study');
    }
}
