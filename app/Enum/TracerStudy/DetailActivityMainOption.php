<?php

namespace App\Enum\TracerStudy;

enum DetailActivityMainOption: string
{
    case WORKING = 'bekerja';
    case UNIVERSITY = 'kuliah';
    case ENTREPRENEUR = 'wirausaha';
    case NOT_YET = 'belum';

    public function label(): string
    {
        return match ($this) {
            self::WORKING => 'Bekerja',
            self::UNIVERSITY => 'Kuliah',
            self::ENTREPRENEUR => 'Wirausaha',
            self::NOT_YET => 'Belum Bekerja / Belum Melanjutkan Studi',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
