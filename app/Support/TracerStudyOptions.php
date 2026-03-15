<?php

namespace App\Support;

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

final class TracerStudyOptions
{
    public static function all(): array
    {
        return [
            'activityMain' => self::group(DetailActivityMainOption::class),
            'studentActivityMain' => self::group(StudentActivityMainOption::class),
            'workType' => self::group(StudentWorkTypeOption::class),
            'salary' => self::group(StudentWorkingSalaryOption::class),
            'jobChange' => self::group(StudentWorkingJobChangeOption::class),
            'howFoundJob' => self::group(StudentWorkingHowFoundJobOption::class),
            'jobRelevance' => self::group(StudentWorkingJobRelevanceOption::class),
            'educationLevel' => self::group(StudentUniversityEducationLevelOption::class),
            'fundingSource' => self::group(StudentUniversityFundingSourceOption::class),
            'majorRelevance' => self::group(StudentUniversityMajorRelevanceOption::class),
            'businessScale' => self::group(StudentEntrepreneurBusinessScaleOption::class),
            'businessIncome' => self::group(StudentEntrepreneurBusinessIncomeOption::class),
            'smkReason' => self::group(StudentFeedbackSmkReasonOption::class),
            'pklDuration' => self::group(StudentFeedbackPklDurationOption::class),
            'pklQuality' => self::group(StudentFeedbackPklQualityOption::class),
            'certificateOwnership' => self::group(StudentFeedbackCertificateOwnership::class),
        ];
    }

    private static function group(string $enumClass): array
    {
        $options = [];
        $labels = [];

        foreach ($enumClass::cases() as $case) {
            $options[] = [
                'key' => $case->value,
                'text' => $case->label(),
            ];

            $labels[$case->value] = $case->label();
        }

        return [
            'options' => $options,
            'labels' => $labels,
        ];
    }
}
