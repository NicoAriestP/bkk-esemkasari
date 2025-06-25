<?php

namespace App\Enum;

enum VacancyApplicationStatus: string
{
    case APPLIED = 'applied';
    case QUALIFIED = 'qualified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::APPLIED => 'Sudah Melamar',
            self::QUALIFIED => 'Lolos Seleksi',
            self::REJECTED => 'Tidak Lolos',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::APPLIED => 'info',
            self::QUALIFIED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
