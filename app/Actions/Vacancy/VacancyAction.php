<?php

namespace App\Actions\Vacancy;

use App\Models\Vacancy;
use Illuminate\Http\Request;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;

class VacancyAction
{
    public function save(CreateVacancyFormRequest $request): Vacancy
    {
        $validated = $request->validated();
        unset($validated['file']);

        $model = new Vacancy($validated);

        if (isset($request->validated()['file'])) {
            $model->updateFile($request->validated()['file']);
        }

        $model->save();

        return $model;
    }

    public function update(Vacancy $model, EditVacancyFormRequest $request): Vacancy
    {
        $validated = $request->validated();
        unset($validated['file']);

        $model->update($validated);

        if (isset($request->validated()['file'])) {
            $model->updateFile($request->validated()['file']);
        }

        return $model;
    }
}
