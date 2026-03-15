<?php

namespace App\Enum\TracerStudy;

enum StudentWorkingJobChangeOption: string
{
    case JC1 = 'jc1';
    case JC2 = 'jc2';
    case JC3 = 'jc3';
    case JC4 = 'jc4';

    public function label(): string
    {
        return match ($this) {
            self::JC1 => 'Belum pernah',
            self::JC2 => 'Satu kali',
            self::JC3 => 'Dua kali',
            self::JC4 => 'Tiga kali atau lebih',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
