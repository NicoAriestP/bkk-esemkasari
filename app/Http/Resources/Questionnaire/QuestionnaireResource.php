<?php

namespace App\Http\Resources\Questionnaire;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
