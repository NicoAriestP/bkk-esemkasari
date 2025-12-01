<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireResponse extends Model
{
    protected $fillable = [
        'questionnaire_id',
        'student_id',
        'submitted_at'
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
