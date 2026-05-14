<?php

namespace App\Enum\TracerStudy;

enum StudentFeedbackSmkReasonOption: string
{
    case R1 = 'r1';
    case R2 = 'r2';
    case R3 = 'r3';
    case R4 = 'r4';
    case R5 = 'r5';
    case R_OTHER = 'smk-other';

    public function label(): string
    {
        return match ($this) {
            self::R1 => 'Ingin cepat mendapatkan pekerjaan',
            self::R2 => 'Keinginan sendiri',
            self::R3 => 'Diajak teman',
            self::R4 => 'Keinginan orang tua/keluarga',
            self::R5 => 'Tidak diterima di sekolah lain',
            self::R_OTHER => 'Lainnya',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
