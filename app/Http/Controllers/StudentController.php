<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Http\Requests\Student\CreateStudentFormRequest;
use App\Http\Requests\Student\EditStudentFormRequest;
use App\Actions\Student\StudentAction;
use App\Models\StudentClass;
use App\Models\Year;

class StudentController extends Controller
{
    public function index(Year $year, StudentClass $studentClass, Request $request)
    {
        $search = $request->input('search', '');

        $students = Student::query()
            ->where('student_class_id', $studentClass->id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                ->orWhere('nisn', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('student/List', [
            'year' => $year,
            'studentClass' => $studentClass,
            'models' => $students,
        ]);
    }

    public function store(Year $year, StudentClass $studentClass, CreateStudentFormRequest $request, StudentAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function update(Year $year, StudentClass $studentClass, EditStudentFormRequest $request, Student $model, StudentAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function destroy(Year $year, StudentClass $studentClass, Student $model)
    {
        $model->delete();

        return redirect()->route('students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }
}
