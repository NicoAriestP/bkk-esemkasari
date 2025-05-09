<?php

namespace App\Actions\Year;

use App\Models\Year;
use Illuminate\Http\Request;
use App\Http\Requests\Year\CreateYearFormRequest;
use App\Http\Requests\Year\EditYearFormRequest;

class YearAction
{
    public function save(CreateYearFormRequest $request): Year
    {
        $validated = $request->validated();

        $model = new Year($validated);

        $model->save();

        return $model;
    }

    public function update(Year $model, EditYearFormRequest $request): Year
    {
        $validated = $request->validated();

        $model->update($validated);

        return $model;
    }
}
