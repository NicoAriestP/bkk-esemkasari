<?php

namespace App\Http\Resources\StudentActivityAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentActivityAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'activity' => $this->activity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
