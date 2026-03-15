<?php

namespace App\Enum\TracerStudy;

enum StudentWorkingHowFoundJobOption: string
{
    case HFJ1 = 'hfj1';
    case HFJ2 = 'hfj2';
    case HFJ3 = 'hfj3';
    case HFJ4 = 'hfj4';
    case HFJ5 = 'hfj5';
    case HFJ6 = 'hfj6';
    case HFJ7 = 'hfj7';
    case HFJ8 = 'hfj8';
    case HFJ_OTHER = 'hfj-other';

    public function label(): string
    {
        return match ($this) {
            self::HFJ1 => 'Melalui industri yang menjadi mitra SMK',
            self::HFJ2 => 'Melalui layanan pusat karir/bursa kerja di SMK',
            self::HFJ3 => 'Melalui tempat magang (Prakerin/PKL) selama di SMK',
            self::HFJ4 => 'Melalui ikatan alumni',
            self::HFJ5 => 'Melalui iklan di media cetak/online',
            self::HFJ6 => 'Melalui bursa kerja/pameran kerja/job fair',
            self::HFJ7 => 'Melalui dinas ketenagakerjaan setempat',
            self::HFJ8 => 'Melalui bantuan orang tua/famili/teman',
            self::HFJ_OTHER => 'Lainnya',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
