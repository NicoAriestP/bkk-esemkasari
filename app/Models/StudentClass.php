<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    /** @use HasFactory<\Database\Factories\StudentClassFactory> */
    use HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'year_id',
        'name'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
