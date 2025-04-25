<?php

namespace App\Http\Resources\Vacancy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_at' => $this->due_at,
            'gender' => $this->gender,
            'location' => $this->location,
            'file' => $this->file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
