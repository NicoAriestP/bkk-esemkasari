<?php

namespace App\Enum\TracerStudy;

enum StudentEntrepreneurBusinessScaleOption: string
{
    case BS1 = 'bs1';
    case BS2 = 'bs2';
    case BS3 = 'bs3';

    public function label(): string
    {
        return match ($this) {
            self::BS1 => 'Usaha Mikro (omzet < 300 juta/tahun)',
            self::BS2 => 'Usaha Kecil (omzet 300 juta - 2.5 miliar/tahun)',
            self::BS3 => 'Usaha Menengah (omzet > 2.5 miliar/tahun)',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
