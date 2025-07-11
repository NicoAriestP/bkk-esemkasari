<?php

namespace App\Exports\Vacancy;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class QualifiedApplicantsExport implements FromView
{
    /**
     * Use Exportable trait to enable export functionality.
     */
    use Exportable;

    private Vacancy $vacancy;

    public function __construct(Vacancy $vacancy)
    {
        $this->vacancy = $vacancy;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.vacancy.qualified_applicants', [
            'vacancy' => $this->vacancy->load('createdBy'),
            'qualifiedApplicants' => VacancyApplication::query()
                ->with('student.studentClass.year')
                ->where('vacancy_id', $this->vacancy->id)
                ->where('status', 'qualified')
                ->get(),
        ]);
    }
}
