<?php

namespace App\Enum;

enum QuestionType: string
{
    case DROPDOWN = 'dropdown';
    case CHECKBOX = 'checkbox';
    case FILLABLE = 'fillable';
    case DATE = 'date';

    public function label(): string
    {
        return match($this) {
            self::DROPDOWN => 'Pilihan',
            self::CHECKBOX => 'Checkbox',
            self::FILLABLE => 'Isian',
            self::DATE => 'Tanggal',
        };
    }
}
