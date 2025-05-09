<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Year;
use App\Http\Requests\Year\CreateYearFormRequest;
use App\Http\Requests\Year\EditYearFormRequest;
use App\Actions\Year\YearAction;

class YearController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $years = Year::query()
            ->when($search, function ($query, $search) {
                $query->where('year', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('year/List', [
            'models' => $years,
        ]);
    }

    public function store(CreateYearFormRequest $request, YearAction $action)
    {
        $model = $action->save($request);

        return redirect()->route('years.index');
    }

    public function update(EditYearFormRequest $request, Year $model, YearAction $action)
    {
        $action->update($model, $request);

        return redirect()->route('years.index');
    }

    public function destroy(Year $model)
    {
        $model->delete();

        return redirect()->route('years.index');
    }
}
