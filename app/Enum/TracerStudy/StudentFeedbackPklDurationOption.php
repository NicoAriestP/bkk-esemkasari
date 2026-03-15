<?php

namespace App\Enum\TracerStudy;

enum StudentFeedbackPklDurationOption: string
{
    case D1 = 'd1';
    case D2 = 'd2';
    case D3 = 'd3';

    public function label(): string
    {
        return match ($this) {
            self::D1 => 'Kurang dari 6 bulan',
            self::D2 => '6 bulan',
            self::D3 => 'Lebih dari 6 bulan',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
