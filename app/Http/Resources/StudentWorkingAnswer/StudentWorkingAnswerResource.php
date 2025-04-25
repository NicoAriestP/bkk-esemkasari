<?php

namespace App\Http\Resources\StudentWorkingAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentWorkingAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'workplace' => $this->workplace,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
