<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vacancy;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;
use App\Actions\Vacancy\VacancyAction;

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
