<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Inertia\Inertia;

class StudentQuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('search', '');
        $itemsPerPage = 10; // Tentukan jumlah item per halaman/permintaan

        // Buat query dasar yang bisa dipakai ulang
        $query = Questionnaire::query()
            ->when($searchQuery, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            // Filter kuisioner yang belum dijawab oleh siswa yang login
            ->whereDoesntHave('responses', function ($query) {
                $query->where('student_id', auth()->guard('student')->id());
            })
            ->orderBy('created_at', 'desc');

        // Cek jika ini adalah permintaan untuk lazy load dari frontend
        if ($request->has('lazy_load')) {
            $offset = $request->input('first', 0);
            $items = (clone $query)->skip($offset)->take($itemsPerPage)->get();

            // Kembalikan data dalam format JSON
            return response()->json(['models' => $items]);
        }

        // Untuk permintaan awal (saat halaman di-load pertama kali)
        return Inertia::render('questionnaire/student/List', [
            // FIX: Tambahkan prop 'totalRecords' yang wajib ada
            'totalRecords' => (clone $query)->count(),

            // Kirim potongan data pertama saja
            'models' => (clone $query)->take($itemsPerPage)->get(),

            // Kirim filter agar nilai pencarian tidak hilang saat reload
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Questionnaire $model)
    {
        $model->load('questionnaireQuestions.questionOptions');

        return Inertia::render('questionnaire/student/Detail', [
            'model' => $model,
        ]);
    }
}
