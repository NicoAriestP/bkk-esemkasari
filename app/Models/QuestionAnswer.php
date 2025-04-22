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
}
