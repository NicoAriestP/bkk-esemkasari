<?php

namespace App\Enum\TracerStudy;

enum StudentUniversityMajorRelevanceOption: string
{
    case MR1 = 'mr1';
    case MR2 = 'mr2';
    case MR3 = 'mr3';
    case MR4 = 'mr4';

    public function label(): string
    {
        return match ($this) {
            self::MR1 => 'Sangat Sesuai',
            self::MR2 => 'Sesuai',
            self::MR3 => 'Kurang Sesuai',
            self::MR4 => 'Tidak Sesuai Sama Sekali',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
