<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vacancy;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;
use App\Actions\Vacancy\VacancyAction;

class VacancyStudentController extends Controller
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
            ->where('created_by', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('vacancy/List', [
            'models' => $vacancies,
        ]);
    }
}
