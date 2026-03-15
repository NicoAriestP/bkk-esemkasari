<?php

namespace App\Enum\TracerStudy;

enum StudentEntrepreneurBusinessIncomeOption: string
{
    case BI1 = 'bi1';
    case BI2 = 'bi2';
    case BI3 = 'bi3';
    case BI4 = 'bi4';

    public function label(): string
    {
        return match ($this) {
            self::BI1 => 'Kurang dari Rp. 2.500.000,-',
            self::BI2 => 'Rp. 2.500.000 s.d Rp. 5.000.000,-',
            self::BI3 => 'Rp. 5.000.001 s.d Rp. 10.000.000,-',
            self::BI4 => 'Lebih dari Rp. 10.000.000,-',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
