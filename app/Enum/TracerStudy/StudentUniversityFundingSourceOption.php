<?php

namespace App\Enum\TracerStudy;

enum StudentUniversityFundingSourceOption: string
{
    case FS1 = 'fs1';
    case FS2 = 'fs2';
    case FS3 = 'fs3';
    case FS4 = 'fs4';
    case FS5 = 'fs5';

    public function label(): string
    {
        return match ($this) {
            self::FS1 => 'Biaya Sendiri / Keluarga',
            self::FS2 => 'Beasiswa KIP-Kuliah',
            self::FS3 => 'Beasiswa Penuh (selain KIP-K)',
            self::FS4 => 'Beasiswa Sebagian (selain KIP-K)',
            self::FS5 => 'Ikatan Dinas',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
