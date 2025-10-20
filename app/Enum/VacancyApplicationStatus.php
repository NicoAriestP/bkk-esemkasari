<?php

namespace App\Enum;

enum VacancyApplicationStatus: string
{
    case APPLIED = 'applied';
    case QUALIFIED = 'qualified';

    public function label(): string
    {
        return match($this) {
            self::APPLIED => 'Sudah Melamar',
            self::QUALIFIED => 'Lolos Seleksi',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::APPLIED => 'info',
            self::QUALIFIED => 'success',
        };
    }
}
