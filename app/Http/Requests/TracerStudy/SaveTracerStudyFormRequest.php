<?php

namespace App\Http\Requests\TracerStudy;

use App\Enum\TracerStudy\DetailActivityMainOption;
use App\Enum\TracerStudy\StudentActivityMainOption;
use App\Enum\TracerStudy\StudentEntrepreneurBusinessIncomeOption;
use App\Enum\TracerStudy\StudentEntrepreneurBusinessScaleOption;
use App\Enum\TracerStudy\StudentFeedbackCertificateOwnership;
use App\Enum\TracerStudy\StudentFeedbackPklDurationOption;
use App\Enum\TracerStudy\StudentFeedbackPklQualityOption;
use App\Enum\TracerStudy\StudentFeedbackSmkReasonOption;
use App\Enum\TracerStudy\StudentUniversityEducationLevelOption;
use App\Enum\TracerStudy\StudentUniversityFundingSourceOption;
use App\Enum\TracerStudy\StudentUniversityMajorRelevanceOption;
use App\Enum\TracerStudy\StudentWorkingHowFoundJobOption;
use App\Enum\TracerStudy\StudentWorkingJobChangeOption;
use App\Enum\TracerStudy\StudentWorkingJobRelevanceOption;
use App\Enum\TracerStudy\StudentWorkingSalaryOption;
use App\Enum\TracerStudy\StudentWorkTypeOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use JsonException;

class SaveTracerStudyFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! Auth::guest();
    }

    public function rules(): array
    {
        return [
            'is_married' => 'required|boolean',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'cv_file' => 'nullable|file|mimes:pdf|max:2048',
            'student_activity_answers' => 'required|json',
            'detail_activity_answers' => 'required|json',
            'student_working_answers' => 'nullable|json',
            'student_university_answers' => 'nullable|json',
            'student_entrepreneur_answers' => 'nullable|json',
            'student_feedback_answers' => 'required|json',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $studentActivity = $this->decodeJsonField($validator, 'student_activity_answers');
            $detailActivity = $this->decodeJsonField($validator, 'detail_activity_answers');
            $working = $this->decodeJsonField($validator, 'student_working_answers', true);
            $university = $this->decodeJsonField($validator, 'student_university_answers', true);
            $entrepreneur = $this->decodeJsonField($validator, 'student_entrepreneur_answers', true);
            $feedback = $this->decodeJsonField($validator, 'student_feedback_answers');

            if ($studentActivity !== null) {
                $this->validateEnumValue($validator, 'student_activity_answers.isStudying', $studentActivity['isStudying'] ?? null, StudentActivityMainOption::values(), true);

                $this->validateEnumValue($validator, 'student_activity_answers.isWorking', $studentActivity['isWorking'] ?? null, StudentActivityMainOption::values());

                $this->validateEnumValue($validator, 'student_activity_answers.workType', $studentActivity['workType'] ?? null, StudentWorkTypeOption::values());
            }

            if ($detailActivity !== null) {
                $this->validateEnumValue($validator, 'detail_activity_answers.mainActivity', $detailActivity['mainActivity'] ?? null, DetailActivityMainOption::values(), true);
            }

            if ($working !== null && $working !== []) {
                $this->validateEnumValue($validator, 'student_working_answers.salaryRange', $working['salaryRange'] ?? null, StudentWorkingSalaryOption::values());
                $this->validateEnumValue($validator, 'student_working_answers.jobChangeFrequency', $working['jobChangeFrequency'] ?? null, StudentWorkingJobChangeOption::values());
                $this->validateEnumValue($validator, 'student_working_answers.jobRelevance', $working['jobRelevance'] ?? null, StudentWorkingJobRelevanceOption::values());

                if (isset($working['workingHours']) && $working['workingHours'] !== null && $working['workingHours'] !== '' && ! is_numeric($working['workingHours'])) {
                    $validator->errors()->add('student_working_answers.workingHours', 'Jam kerja harus berupa angka.');
                }

                if (isset($working['howFoundFirstJob']) && ! is_array($working['howFoundFirstJob'])) {
                    $validator->errors()->add('student_working_answers.howFoundFirstJob', 'Pilihan cara mendapat pekerjaan harus berupa array.');
                }

                if (isset($working['howFoundFirstJob']) && is_array($working['howFoundFirstJob'])) {
                    foreach ($working['howFoundFirstJob'] as $index => $item) {
                        $this->validateEnumValue(
                            $validator,
                            sprintf('student_working_answers.howFoundFirstJob.%s', $index),
                            $item,
                            StudentWorkingHowFoundJobOption::values(),
                            true,
                        );
                    }
                }
            }

            if ($university !== null && $university !== []) {
                $this->validateEnumValue($validator, 'student_university_answers.educationLevel', $university['educationLevel'] ?? null, StudentUniversityEducationLevelOption::values());
                $this->validateEnumValue($validator, 'student_university_answers.fundingSource', $university['fundingSource'] ?? null, StudentUniversityFundingSourceOption::values());
                $this->validateEnumValue($validator, 'student_university_answers.majorRelevance', $university['majorRelevance'] ?? null, StudentUniversityMajorRelevanceOption::values());
            }

            if ($entrepreneur !== null && $entrepreneur !== []) {
                $this->validateEnumValue($validator, 'student_entrepreneur_answers.businessScale', $entrepreneur['businessScale'] ?? null, StudentEntrepreneurBusinessScaleOption::values());
                $this->validateEnumValue($validator, 'student_entrepreneur_answers.businessIncome', $entrepreneur['businessIncome'] ?? null, StudentEntrepreneurBusinessIncomeOption::values());
            }

            if ($feedback !== null) {
                $this->validateEnumValue($validator, 'student_feedback_answers.pklDuration', $feedback['pklDuration'] ?? null, StudentFeedbackPklDurationOption::values());
                $this->validateEnumValue($validator, 'student_feedback_answers.hasCertificate', $feedback['hasCertificate'] ?? null, StudentFeedbackCertificateOwnership::values());

                if (isset($feedback['smkReasons']) && ! is_array($feedback['smkReasons'])) {
                    $validator->errors()->add('student_feedback_answers.smkReasons', 'Pilihan alasan masuk SMK harus berupa array.');
                }

                if (isset($feedback['smkReasons']) && is_array($feedback['smkReasons'])) {
                    foreach ($feedback['smkReasons'] as $index => $item) {
                        $this->validateEnumValue(
                            $validator,
                            sprintf('student_feedback_answers.smkReasons.%s', $index),
                            $item,
                            StudentFeedbackSmkReasonOption::values(),
                            true,
                        );
                    }
                }

                if (isset($feedback['pklQuality']) && ! is_array($feedback['pklQuality'])) {
                    $validator->errors()->add('student_feedback_answers.pklQuality', 'Data kualitas PKL tidak valid.');
                }

                if (isset($feedback['pklQuality']) && is_array($feedback['pklQuality'])) {
                    $this->validateEnumValue($validator, 'student_feedback_answers.pklQuality.location', $feedback['pklQuality']['location'] ?? null, StudentFeedbackPklQualityOption::values());
                    $this->validateEnumValue($validator, 'student_feedback_answers.pklQuality.taskRelevance', $feedback['pklQuality']['taskRelevance'] ?? null, StudentFeedbackPklQualityOption::values());
                    $this->validateEnumValue($validator, 'student_feedback_answers.pklQuality.guidance', $feedback['pklQuality']['guidance'] ?? null, StudentFeedbackPklQualityOption::values());
                    $this->validateEnumValue($validator, 'student_feedback_answers.pklQuality.monitoring', $feedback['pklQuality']['monitoring'] ?? null, StudentFeedbackPklQualityOption::values());
                }
            }
        });
    }

    private function decodeJsonField(Validator $validator, string $field, bool $nullable = false): ?array
    {
        $rawValue = $this->input($field);

        if ($nullable && ($rawValue === null || $rawValue === '')) {
            return null;
        }

        if (! is_string($rawValue)) {
            $validator->errors()->add($field, 'Format data tidak valid.');
            return null;
        }

        try {
            $decoded = json_decode($rawValue, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $validator->errors()->add($field, 'Format JSON tidak valid.');
            return null;
        }

        if (! is_array($decoded)) {
            $validator->errors()->add($field, 'Struktur data harus berupa object JSON.');
            return null;
        }

        return $decoded;
    }

    private function validateEnumValue(
        Validator $validator,
        string $field,
        mixed $value,
        array $allowedValues,
        bool $required = false,
        bool $treatEmptyStringAsNull = true,
    ): void {
        if ($value === '' && $treatEmptyStringAsNull) {
            $value = null;
        }

        if ($value === null) {
            if ($required) {
                $validator->errors()->add($field, 'Field wajib diisi.');
            }
            return;
        }

        if (! in_array($value, $allowedValues, true)) {
            $validator->errors()->add($field, 'Pilihan tidak valid.');
        }
    }
}
