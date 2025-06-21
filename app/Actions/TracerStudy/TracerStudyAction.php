<?php

namespace App\Actions\TracerStudy;

use App\Http\Requests\TracerStudy\SaveTracerStudyFormRequest;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class TracerStudyAction
{
    public function save(Student $student, SaveTracerStudyFormRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Gunakan hasFile dan file() untuk praktik yang lebih aman
            if ($request->hasFile('cv_file')) {
                // Asumsi nama methodnya adalah updateCvFile, sesuaikan jika berbeda
                $student->updateCvFile($request->file('cv_file'));
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
            if ($mainActivity === 'bekerja' && !empty($validated['student_working_answers']) && $validated['student_working_answers'] !== '{}') {
                $student->studentWorkingAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_working_answers']]
                );
                // Hapus data lain jika ada
                $student->studentUniversityAnswer()->delete();
                $student->studentEntrepreneurAnswer()->delete();

            } elseif ($mainActivity === 'kuliah' && !empty($validated['student_university_answers']) && $validated['student_university_answers'] !== '{}') {
                $student->studentUniversityAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_university_answers']]
                );
                // Hapus data lain jika ada
                $student->studentWorkingAnswer()->delete();
                $student->studentEntrepreneurAnswer()->delete();

            } elseif ($mainActivity === 'wirausaha' && !empty($validated['student_entrepreneur_answers']) && $validated['student_entrepreneur_answers'] !== '{}') {
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
