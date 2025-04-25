<?php

namespace App\Http\Resources\DetailActivityAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailActivityAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_activity_answer_id' => $this->student_activity_answer_id,
            'question' => $this->question,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
