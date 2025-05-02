<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

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
        ];
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }
}
