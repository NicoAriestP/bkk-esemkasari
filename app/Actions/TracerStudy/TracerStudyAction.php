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
            unset($validated['cv_file']);

            if (isset($request->validated()['cv_file'])) {
                $student->updateCvFile($request->validated()['cv_file']);
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

            $student->studentActivityAnswer()->updateOrCreate(
                ['student_id' => $student->id],
                ['answers' => $validated['student_activity_answers']]
            );

            $student->detailActivityAnswer()->updateOrCreate(
                ['student_id' => $student->id],
                ['answers' => $validated['detail_activity_answers']]
            );

            if (!empty($validated['student_working_answers']) && $validated['student_working_answers'] !== '{}') {
                $student->studentWorkingAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_working_answers']]
                );
            }

            if (!empty($validated['student_university_answers']) && $validated['student_university_answers'] !== '{}') {
                $student->studentUniversityAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_university_answers']]
                );
            }

            if (!empty($validated['student_entrepreneur_answers']) && $validated['student_entrepreneur_answers'] !== '{}') {
                $student->studentEntrepreneurAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_entrepreneur_answers']]
                );
            }

            if (!empty($validated['student_feedback_answers']) && $validated['student_feedback_answers'] !== '{}') {
                $student->feedbackAnswer()->updateOrCreate(
                    ['student_id' => $student->id],
                    ['answers' => $validated['student_feedback_answers']]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
