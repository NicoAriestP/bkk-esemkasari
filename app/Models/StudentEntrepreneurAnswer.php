<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEntrepreneurAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\StudentEntrepreneurAnswerFactory> */
    use HasFactory;

    protected $table = 'student_entrepreneur_answers';

    protected $fillable = [
        'student_id',
        'answers',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
