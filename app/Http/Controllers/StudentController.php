<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Year;
use App\Http\Requests\Student\CreateStudentFormRequest;
use App\Http\Requests\Student\EditStudentFormRequest;
use App\Http\Requests\ImportStudentRequest;
use App\Actions\Student\StudentAction;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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

        return redirect()->route('years.student-classes.students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function update(Year $year, StudentClass $studentClass, EditStudentFormRequest $request, Student $model, StudentAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('years.student-classes.students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function destroy(Year $year, StudentClass $studentClass, Student $model)
    {
        $model->deleteCvFile();
        $model->delete();

        return redirect()->route('years.student-classes.students.index', [
            'year' => $year->id,
            'studentClass' => $studentClass->id,
        ]);
    }

    public function import(ImportStudentRequest $request, StudentClass $studentClass)
    {
        $studentClassId = $studentClass->id;

        try {
            // Import dengan student_class_id default
            $import = new StudentsImport($studentClassId);
            $import->import($request->file('file'));

            return back()->with('success', 'Data siswa berhasil diimpor.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function dashboard(Request $request)
    {
        $dashboardData = Student::getDashboardData();

        return Inertia::render('dashboard/DashboardStudent', [
            'dashboardData' => $dashboardData
        ]);
    }
}
