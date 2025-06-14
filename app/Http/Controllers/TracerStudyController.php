<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\StudentActivityAnswer;
use App\Models\StudentUniversityAnswer;
use App\Models\StudentWorkingAnswer;
use App\Models\FeedbackAnswer;
use App\Models\DetailActivityAnswer;

class TracerStudyController extends Controller
{
    public function tracerStudy(Request $request)
    {
        return Inertia::render('tracerStudy/TracerStudy');
    }

    // public function saveTracerStudy(Request $request)
    // {
    //     $model = $request->all();

    //     return redirect()->route('users.index');
    // }
}
