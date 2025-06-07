<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vacancy;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;
use App\Actions\Vacancy\VacancyAction;

class VacancyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $vacancies = Vacancy::query()
            ->with('createdBy')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%$search%")
                ->orWhere('location', 'like', "%$search%")
                ->orWhereHas('createdBy', function ($query, $search) {
                    $query->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('vacancy/List', [
            'models' => $vacancies,
        ]);
    }

    public function create()
    {
        return Inertia::render('vacancy/Form', [
            'isNewRecord' => true,
        ]);
    }

    public function edit(Vacancy $model)
    {
        return Inertia::render('vacancy/Form', [
            'isNewRecord' => false,
            'model' => $model,
        ]);
    }

    public function store(CreateVacancyFormRequest $request, VacancyAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('vacancies.edit', $model->id);
    }

    public function update(EditVacancyFormRequest $request, Vacancy $model, VacancyAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('vacancies.edit', $model->id);
    }

    public function destroy(Vacancy $model)
    {
        $model->delete();

        return redirect()->route('vacancies.index');
    }
}
