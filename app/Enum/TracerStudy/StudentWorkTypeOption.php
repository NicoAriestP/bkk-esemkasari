<?php

namespace App\Enum\TracerStudy;

enum StudentWorkTypeOption: string
{
    case SELF_EMPLOYED_SOLO = 'Berwirausaha sendiri tanpa dibantu orang lain';
    case SELF_EMPLOYED_UNPAID_HELP = 'Berwirausaha dengan dibantu buruh/pekerja tak dibayar';
    case SELF_EMPLOYED_PAID_HELP = 'Berwirausaha dengan dibantu buruh/pekerja dibayar';
    case HELPING_FAMILY_BUSINESS = 'Membantu menjalankan usaha/wirausaha keluarga';
    case EMPLOYEE = 'Buruh/karyawan/pegawai';
    case FREELANCER = 'Pekerja bebas (tidak punya majikan tetap)';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
