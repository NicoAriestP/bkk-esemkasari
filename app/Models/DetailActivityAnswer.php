<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailActivityAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\DetailActivityAnswerFactory> */

    protected $table = 'detail_activity_answers';

    protected $casts = [
        'answers' => 'array',
    ];

    protected $fillable = [
        'student_id',
        'answers',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
