<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasFeaturedFile;
use Illuminate\Support\Facades\Storage;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasFeaturedFile;

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
        'cv_file',
        'born_date',
        'height',
        'weight',
        'province',
        'city',
        'address',
        'is_graduated',
        'is_married'
    ];

    protected $useTypeForFileFolderName = true;
    protected $type = 'cv_file';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'is_graduated_label',
        'is_married_label',
        'cv_file_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_graduated' => 'boolean',
            'is_married' => 'boolean',
        ];
    }

    protected function isGraduatedLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_graduated == true ? 'Lulus' : 'Belum Lulus',
        );
    }

    protected function isMarriedLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_married == true ? 'Menikah' : 'Belum Menikah',
        );
    }

    /**
     * Provide web accessible image url.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function cvFileUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->cv_file
                ? Storage::disk(config('filesystems.default', 'public'))->url($this->cv_file)
                : null,
        );
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function studentActivityAnswer()
    {
        return $this->hasOne(StudentActivityAnswer::class);
    }

    public function studentUniversityAnswer()
    {
        return $this->hasOne(StudentUniversityAnswer::class);
    }

    public function studentWorkingAnswer()
    {
        return $this->hasOne(StudentWorkingAnswer::class);
    }

    public function feedbackAnswer()
    {
        return $this->hasOne(FeedbackAnswer::class);
    }

    public function detailActivityAnswer()
    {
        return $this->hasOne(DetailActivityAnswer::class);
    }

    public function studentEntrepreneurAnswer()
    {
        return $this->hasOne(StudentEntrepreneurAnswer::class);
    }

    public function vacancyApplication()
    {
        return $this->hasMany(VacancyApplication::class);
    }
}
