<?php

namespace App\Actions\Vacancy;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;
use App\Http\Requests\Vacancy\CreateVacancyFormRequest;
use App\Http\Requests\Vacancy\EditVacancyFormRequest;
use App\Http\Requests\Vacancy\SaveVacancyApplicationsFormRequest;
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

        public function saveApplications(Vacancy $model, SaveVacancyApplicationsFormRequest $request): Vacancy
        {
            $applicantIds = $request->validated('applicant_ids');

            // Ambil semua aplikasi yang sebelumnya memiliki status QUALIFIED untuk lowongan ini
            $previousQualifiedApplications = VacancyApplication::where('vacancy_id', $model->id)
                ->where('status', VacancyApplicationStatus::QUALIFIED)
                ->get();

            // Update status menjadi APPLIED untuk siswa yang tidak lagi ada di applicant_ids
            foreach ($previousQualifiedApplications as $application) {
                if (!in_array($application->student_id, $applicantIds)) {
                    $application->update(['status' => VacancyApplicationStatus::APPLIED]);
                }
            }

            // Buat atau update aplikasi untuk applicant_ids yang dikirim
            foreach ($applicantIds as $studentId) {
                VacancyApplication::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'vacancy_id' => $model->id,
                    ],
                    [
                        'status' => VacancyApplicationStatus::QUALIFIED,
                    ]
                );
            }

            return $model;
        }
    }
