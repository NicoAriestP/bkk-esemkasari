<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enum\VacancyApplicationStatus;

class VacancyApplication extends Model
{
    /** @use HasFactory<\Database\Factories\VacancyApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'vacancy_id',
        'status'
    ];

    protected $casts = [
        'status' => VacancyApplicationStatus::class
    ];

    public function getStatusLabelAttribute()
    {
        return $this->status->label();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
}
