<?php

namespace App\Enum\TracerStudy;

enum StudentUniversityEducationLevelOption: string
{
    case D1 = 'd1';
    case D2 = 'd2';
    case D3 = 'd3';
    case S1D4 = 's1d4';
    case S1 = 's1';

    public function label(): string
    {
        return match ($this) {
            self::D1 => 'D1 (Diploma 1)',
            self::D2 => 'D2 (Diploma 2)',
            self::D3 => 'D3 (Diploma 3)',
            self::S1D4 => 'S1 Terapan / D4 (Sarjana Terapan)',
            self::S1 => 'S1 (Sarjana)',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
