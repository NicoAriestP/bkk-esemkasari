<?php

namespace App\Http\Resources\DetailActivityAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DetailActivityAnswerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
