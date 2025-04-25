<?php

namespace App\Http\Resources\QuestionAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'questionnaire_question_id' => $this->questionnaire_question_id,
            'student_id' => $this->student_id,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
