<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentWorkingAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\StudentWorkingAnswerFactory> */

    protected $table = 'student_working_answers';

    protected $fillable = [
        'student_id',
        'answers',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
