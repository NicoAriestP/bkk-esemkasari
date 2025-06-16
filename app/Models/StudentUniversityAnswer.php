<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUniversityAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\StudentUniversityAnswerFactory> */
    use HasFactory;

    protected $table = 'student_university_answers';

    protected $fillable = [
        'student_id',
        'answers',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
