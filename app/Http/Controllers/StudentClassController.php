<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\StudentClass;
use App\Http\Requests\StudentClass\CreateStudentClassFormRequest;
use App\Http\Requests\StudentClass\EditStudentClassFormRequest;
use App\Actions\StudentClass\StudentClassAction;
use App\Models\Year;

class StudentClassController extends Controller
{
    public function index(Year $year, Request $request)
    {
        $search = $request->input('search', '');

        $studentClasses = StudentClass::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->where('year_id', $year->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('studentClass/List', [
            'models' => $studentClasses,
            'year' => $year,
        ]);
    }

    public function store(Year $year, CreateStudentClassFormRequest $request, StudentClassAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('years.student-classes.index', $year->id);
    }

    public function update(Year $year, EditStudentClassFormRequest $request, StudentClass $model, StudentClassAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('years.student-classes.index', $year->id);
    }

    public function destroy(Year $year, StudentClass $model)
    {
        $model->delete();

        return redirect()->route('years.student-classes.index', $year->id);
    }
}
