<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\Blameable;

class Questionnaire extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'created_by',
        'updated_by',
        'title',
        'description',
        'type',
        'due_at'
    ];

    public function questionnaireQuestions()
    {
        return $this->hasMany(QuestionnaireQuestion::class);
    }
}
