<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\Blameable;

class QuestionnaireQuestion extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'created_by',
        'updated_by',
        'questionnaire_id',
        'question',
        'type',
        'notes',
        'is_multiple_answers'
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class);
    }
}
