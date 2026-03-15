<?php

namespace App\Enum\TracerStudy;

enum StudentActivityMainOption: string
{
    case STUDYING = 'Ya';
    case NOT_STUDYING = 'Tidak';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
