<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'questionnaire_id',
        'question',
        'type',
        'notes',
        'is_multiple_answers'
    ];
}
