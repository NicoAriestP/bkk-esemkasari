<?php

namespace App\Enum\TracerStudy;

enum StudentFeedbackPklQualityOption: string
{
    case Q1 = 'q1';
    case Q2 = 'q2';
    case Q3 = 'q3';
    case Q4 = 'q4';

    public function label(): string
    {
        return match ($this) {
            self::Q1 => 'Sangat Tidak Baik',
            self::Q2 => 'Tidak Baik',
            self::Q3 => 'Baik',
            self::Q4 => 'Sangat Baik',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
