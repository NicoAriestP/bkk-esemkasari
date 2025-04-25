<?php

namespace App\Http\Resources\FeedbackAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'feedback' => $this->feedback,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
