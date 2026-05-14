<?php

namespace App\Exports\Student;

use App\Models\Student;
use App\Models\StudentClass;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TracerStudyExport implements FromView, ShouldAutoSize
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
        return view('exports.student.tracer-study', [
            'studentClass' => $this->studentClass->load('year'),
            'students' => Student::query()
                ->with([
                    'studentActivityAnswer',
                    'studentUniversityAnswer',
                    'studentWorkingAnswer',
                    'feedbackAnswer',
                    'detailActivityAnswer',
                    'studentEntrepreneurAnswer',
                ])
                ->where('student_class_id', $this->studentClass->id)
                ->whereHas('studentActivityAnswer')
                ->get(),
        ]);
    }
}
