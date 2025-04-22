<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'title',
        'description',
        'due_at',
        'gender',
        'location',
        'file'
    ];
}
