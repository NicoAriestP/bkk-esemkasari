<?php

namespace App\Actions\TracerStudy;

use App\Http\Requests\TracerStudy\SaveTracerStudyFormRequest;
use App\Enum\TracerStudy\DetailActivityMainOption;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class TracerStudyAction
{
    public function save(Student $student, SaveTracerStudyFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Upload CV jika ada file
            if ($request->hasFile('cv_file')) {
                $file = $request->file('cv_file');
                if (! $file->isValid()) {
                    \Log::error('CV upload invalid', ['student_id' => $student->id, 'error' => $file->getError()]);
                    throw new \RuntimeException('Upload CV gagal: file tidak valid. Periksa format dan ukuran file.');
                }

                try {
                    $student->updateCvFile($file);
                } catch (\Throwable $e) {
                    \Log::error('CV storage failed', ['student_id' => $student->id, 'error' => $e->getMessage()]);
                    throw $e;
                }
            }

            $student->update([
                'is_married' => $validated['is_married'],
                'province'   => $validated['province'],
                'city'       => $validated['city'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'],
                'address'    => $validated['address'],
                'height'     => $validated['height'],
                'weight'     => $validated['weight'],
            ]);

            // Selalu simpan jawaban umum
            $student->studentActivityAnswer()->updateOrCreate(
                ['student_id' => $student->id],
                ['answers' => $validated['student_activity_answers']]
            );

            $student->detailActivityAnswer()->updateOrCreate(
                ['student_id' => $student->id],
                ['answers' => $validated['detail_activity_answers']]
            );

            $student->feedbackAnswer()->updateOrCreate(
                ['student_id' => $student->id],
                ['answers' => $validated['student_feedback_answers']]
            );

            // --- PERUBAHAN UTAMA: Logika untuk memastikan hanya satu aktivitas utama ---

            // 1. Dapatkan pilihan aktivitas utama dari data JSON
            $detailActivityData = json_decode($validated['detail_activity_answers'], true);
            $mainActivity = $detailActivityData['mainActivity'] ?? null;

            // 2. Simpan data yang relevan dan HAPUS data yang tidak relevan lagi
            if ($mainActivity === DetailActivityMainOption::WORKING->value && !empty($validated['student_working_answers']) && $validated['student_working_answers'] !== '{}') {
                $student->studentWorkingAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_working_answers']]
                );
                // Hapus data lain jika ada
                $student->studentUniversityAnswer()->delete();
                $student->studentEntrepreneurAnswer()->delete();
            } elseif ($mainActivity === DetailActivityMainOption::UNIVERSITY->value && !empty($validated['student_university_answers']) && $validated['student_university_answers'] !== '{}') {
                $student->studentUniversityAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_university_answers']]
                );
                // Hapus data lain jika ada
                $student->studentWorkingAnswer()->delete();
                $student->studentEntrepreneurAnswer()->delete();
            } elseif ($mainActivity === DetailActivityMainOption::ENTREPRENEUR->value && !empty($validated['student_entrepreneur_answers']) && $validated['student_entrepreneur_answers'] !== '{}') {
                $student->studentEntrepreneurAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_entrepreneur_answers']]
                );
                // Hapus data lain jika ada
                $student->studentWorkingAnswer()->delete();
                $student->studentUniversityAnswer()->delete();
            } else {
                // Jika aktivitasnya adalah 'belum' atau null, hapus semua data aktivitas spesifik
                $student->studentWorkingAnswer()->delete();
                $student->studentUniversityAnswer()->delete();
                $student->studentEntrepreneurAnswer()->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
