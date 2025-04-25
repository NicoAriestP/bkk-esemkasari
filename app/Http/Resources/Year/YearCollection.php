<?php

namespace App\Http\Resources\Year;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class YearCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
