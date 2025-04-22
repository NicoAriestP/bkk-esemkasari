<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'student_class_id',
        'name',
        'nisn',
        'phone',
        'email',
        'password',
        'gender',
        'born_date',
        'height',
        'weight',
        'province',
        'city',
        'address',
        'is_graduated',
        'is_married'
    ];
}
