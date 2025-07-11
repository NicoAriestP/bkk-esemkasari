<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Vacancy;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;
use App\Http\Requests\Vacancy\SaveVacancyApplicationsFormRequest;
use App\Actions\Vacancy\VacancyAction;
use App\Exports\Vacancy\QualifiedApplicantsExport;

class VacancyPartnerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $vacancies = Vacancy::query()
            ->with('createdBy')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('location', 'like', "%$search%")
                    ->orWhereHas('createdBy', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            })
            ->where('created_by', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('vacancy/partner/List', [
            'models' => $vacancies,
        ]);
    }

    public function applications(Request $request, Vacancy $model)
    {
        $search = $request->input('search', '');

        $applicants = $model->vacancyApplication()
            ->with('student.studentClass.year')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('student', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhereHas('studentClass', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%")
                                ->orWhereHas('year', function ($query) use ($search) {
                                    $query->where('year', 'like', "%$search%");
                                });
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('vacancy/partner/VacancyApplications', [
            'vacancy' => $model,
            'applicants' => $applicants,
        ]);
    }

    public function exportApplications(Vacancy $model)
    {
        return (new QualifiedApplicantsExport($model))
            ->download("Seleksi Pelamar - {$model->title} - " . \Carbon\Carbon::now()->format('l, d-m-Y H-i') . ".xlsx");
    }

    public function saveApplications(Vacancy $model, SaveVacancyApplicationsFormRequest $request, VacancyAction $action)
    {
        $model = $action->saveApplications($model, $request);

        return redirect()->route('partners.vacancies.applications', $model->id);
    }

    public function create()
    {
        return Inertia::render('vacancy/partner/Form', [
            'isNewRecord' => true,
        ]);
    }

    public function edit(Vacancy $model)
    {
        return Inertia::render('vacancy/partner/Form', [
            'isNewRecord' => false,
            'model' => $model,
        ]);
    }

    public function store(CreateVacancyFormRequest $request, VacancyAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('partners.vacancies.edit', $model->id);
    }

    public function update(EditVacancyFormRequest $request, Vacancy $model, VacancyAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('partners.vacancies.edit', $model->id);
    }

    public function destroy(Vacancy $model)
    {
        $model->delete();

        return redirect()->route('partners.vacancies.index');
    }
}
