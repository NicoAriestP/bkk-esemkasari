<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'year'
    ];

    protected $appends = [
        'student_classes_count'
    ];

    public function getStudentClassesCountAttribute()
    {
        return $this->studentClasses()->count();
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class);
    }
}
