<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\StudentClass;
use App\Http\Requests\StudentClass\CreateStudentClassFormRequest;
use App\Http\Requests\StudentClass\EditStudentClassFormRequest;
use App\Actions\StudentClass\StudentClassAction;

class StudentClassController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $studentClasses = StudentClass::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('studentClass/List', [
            'models' => $studentClasses,
        ]);
    }

    public function create()
    {
        return Inertia::render('studentClass/Form', [
            'model' => new StudentClass(),
            'isNewRecord' => true,
        ]);
    }

    public function store(CreateStudentClassFormRequest $request, StudentClassAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('student-classes.index');
    }

    public function edit(StudentClass $model)
    {
        return Inertia::render('studentClass/Form', [
            'model' => $model,
            'isNewRecord' => false,
        ]);
    }

    public function update(EditStudentClassFormRequest $request, StudentClass $model, StudentClassAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('student-classes.index');
    }

    public function destroy(StudentClass $model)
    {
        $model->delete();

        return redirect()->route('student-classes.index');
    }
}
