<?php

namespace App\Exports\Student;

use App\Models\Student;
use App\Models\StudentClass;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class StudentsExport implements FromView
{
    /**
     * Use Exportable trait to enable export functionality.
     */
    use Exportable;

    private StudentClass $studentClass;

    public function __construct(StudentClass $studentClass)
    {
        $this->studentClass = $studentClass;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.student.students', [
            'studentClass' => $this->studentClass->load('year'),
            'students' => Student::query()
                ->where('student_class_id', $this->studentClass->id)
                ->get(),
        ]);
    }
}
