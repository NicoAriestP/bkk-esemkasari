<?php

namespace App\Enum\TracerStudy;

enum StudentFeedbackCertificateOwnership: string
{
    case YES = 'Ya';
    case NO = 'Tidak';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
