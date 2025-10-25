<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'question_id',
        'question_option_id',
        'text_answer',
        'is_selected'
    ];

    protected $casts = [
        'is_selected' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'question_id');
    }

    public function questionOption()
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Student::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Student::class, 'updated_by');
    }
}
