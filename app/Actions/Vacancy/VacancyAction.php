<?php

namespace App\Actions\Vacancy;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Enum\VacancyApplicationStatus;

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

    public function applyVacancy(Vacancy $model): Vacancy
    {
        $studentId = Auth::guard('student')->user()->id;

        // Cek apakah siswa sudah pernah melamar lowongan ini sebelumnya
        $existingApplication = VacancyApplication::where('student_id', $studentId)
            ->where('vacancy_id', $model->id)
            ->first();

        if ($existingApplication) {
            throw new \Exception('Anda sudah melamar lowongan ini sebelumnya');
        }

        $vacancyApplication = VacancyApplication::create([
            'student_id' => $studentId,
            'vacancy_id' => $model->id,
            'status' => VacancyApplicationStatus::APPLIED,
        ]);

        return $model;
    }
}
