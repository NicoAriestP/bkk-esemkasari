<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Vacancy;
use App\Traits\Model\Blameable;

class Partner extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, Blameable;

    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'phone',
        'email',
        'password',
        'address'
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

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class);
    }
}
