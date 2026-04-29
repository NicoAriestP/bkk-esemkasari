<?php

namespace App\Enum\TracerStudy;

enum StudentWorkTypeOption: string
{
    case SELF_EMPLOYED_SOLO = 'wt1';
    case SELF_EMPLOYED_UNPAID_HELP = 'wt2';
    case SELF_EMPLOYED_PAID_HELP = 'wt3';
    case HELPING_FAMILY_BUSINESS = 'wt4';
    case EMPLOYEE = 'wt5';
    case FREELANCER = 'wt6';

    public function label(): string
    {
        return match ($this) {
            self::SELF_EMPLOYED_SOLO => 'Berwirausaha sendiri tanpa dibantu orang lain',
            self::SELF_EMPLOYED_UNPAID_HELP => 'Berwirausaha dengan dibantu buruh/pekerja tak dibayar',
            self::SELF_EMPLOYED_PAID_HELP => 'Berwirausaha dengan dibantu buruh/pekerja dibayar',
            self::HELPING_FAMILY_BUSINESS => 'Membantu menjalankan usaha/wirausaha keluarga',
            self::EMPLOYEE => 'Buruh/karyawan/pegawai',
            self::FREELANCER => 'Pekerja bebas (tidak punya majikan tetap)',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
