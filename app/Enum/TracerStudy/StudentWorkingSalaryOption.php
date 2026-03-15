<?php

namespace App\Enum\TracerStudy;

enum StudentWorkingSalaryOption: string
{
    case S1 = 's1';
    case S2 = 's2';
    case S3 = 's3';
    case S4 = 's4';

    public function label(): string
    {
        return match ($this) {
            self::S1 => 'Kurang dari Rp. 1.530.183,-',
            self::S2 => 'Rp. 1.530.183 s.d Rp. 2.040.243,-',
            self::S3 => 'Rp. 2.040.244 s.d Rp. 2.448.292,-',
            self::S4 => 'Lebih dari Rp. 2.448.292,-',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
