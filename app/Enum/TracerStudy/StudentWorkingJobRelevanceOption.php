<?php

namespace App\Enum\TracerStudy;

enum StudentWorkingJobRelevanceOption: string
{
    case JR1 = 'jr1';
    case JR2 = 'jr2';
    case JR3 = 'jr3';
    case JR4 = 'jr4';

    public function label(): string
    {
        return match ($this) {
            self::JR1 => 'Sangat tidak selaras',
            self::JR2 => 'Tidak Selaras',
            self::JR3 => 'Selaras',
            self::JR4 => 'Sangat selaras',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
