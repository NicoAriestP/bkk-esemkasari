<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Http\Requests\Student\CreateStudentFormRequest;
use App\Http\Requests\Student\EditStudentFormRequest;
use App\Actions\Student\StudentAction;
use App\Models\StudentClass;

class StudentController extends Controller
{
    public function index(StudentClass $studentClass, Request $request)
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
            'studentClass' => $studentClass,
            'models' => $students,
        ]);
    }

    public function create()
    {
        return Inertia::render('student/Form', [
            'model' => new Student(),
            'isNewRecord' => true,
        ]);
    }

    public function store(CreateStudentFormRequest $request, StudentAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('student.edit', $model->id);
    }

    public function edit(Student $model)
    {
        return Inertia::render('student/Form', [
            'model' => $model,
            'isNewRecord' => false,
        ]);
    }

    public function update(EditStudentFormRequest $request, Student $model, StudentAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('student.edit', $model->id);
    }

    public function destroy(Student $model)
    {
        $model->delete();

        return redirect()->route('student.index');
    }
}
