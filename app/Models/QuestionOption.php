<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\Blameable;

class QuestionOption extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'created_by',
        'updated_by',
        'question_id',
        'option_label'
    ];

    public function questionnaireQuestion()
    {
        return $this->belongsTo(QuestionnaireQuestion::class);
    }

    public function questionAnswers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }
}
