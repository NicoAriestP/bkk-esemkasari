<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

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
            get: fn () => $this->is_graduated == true ? 'Lulus' : 'Belum Lulus',
        );
    }

    protected function isMarriedLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_married == true ? 'Menikah' : 'Belum Menikah',
        );
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }
}
