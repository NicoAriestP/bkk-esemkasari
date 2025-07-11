<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Vacancy;
use App\Actions\Vacancy\VacancyAction;

class VacancyStudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $itemsPerPage = 8; // Tentukan jumlah item per halaman/permintaan

        $query = Vacancy::query()
            ->with('createdBy')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%$search%")
                        ->orWhere('location', 'like', "%$search%")
                        ->orWhereHas('createdBy', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        });
                });
            })
            ->where('due_at', '>=', now())
            ->orderBy('created_at', 'desc');

        // Cek jika ini adalah permintaan untuk lazy load dari frontend
        if ($request->has('lazy_load')) {
            $offset = $request->input('first', 0);
            $items = (clone $query)->skip($offset)->take($itemsPerPage)->get();

            // Kembalikan data dalam format JSON
            return response()->json(['models' => $items]);
        }

        return Inertia::render('vacancy/student/List', [
            // FIX: Tambahkan prop 'totalRecords' yang wajib ada
            'totalRecords' => (clone $query)->count(),

            // Kirim potongan data pertama saja
            'models' => (clone $query)->take($itemsPerPage)->get(),

            // Kirim filter agar nilai pencarian tidak hilang saat reload
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Vacancy $model)
    {
        $model->load('createdBy', 'vacancyApplication.student');

        return Inertia::render('vacancy/student/Detail', [
            'model' => $model,
            'applicationStatus' => $model->vacancyApplication()->where('student_id', auth()->user()->id)->first()?->status,
        ]);
    }

    public function applyVacancy(Vacancy $model, VacancyAction $action)
    {
        $action->applyVacancy($model);

        return redirect()->route('students.vacancies.show', $model->id);
    }
}
