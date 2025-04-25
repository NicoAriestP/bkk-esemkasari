<?php

namespace App\Http\Resources\Vacancy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VacancyCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
