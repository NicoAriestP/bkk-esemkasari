<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentActivityAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\StudentActivityAnswerFactory> */

    protected $table = 'student_activity_answers';

    protected $fillable = [
        'student_id',
        'answers',
    ];
}
