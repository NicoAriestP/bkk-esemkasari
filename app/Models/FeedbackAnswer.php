<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\FeedbackAnswerFactory> */

    protected $table = 'feedback_answers';

    protected $fillable = [
        'student_id',
        'answers',
    ];
}
