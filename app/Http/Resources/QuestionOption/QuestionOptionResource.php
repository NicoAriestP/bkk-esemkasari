<?php

namespace App\Http\Resources\QuestionOption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'questionnaire_question_id' => $this->questionnaire_question_id,
            'option' => $this->option,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
