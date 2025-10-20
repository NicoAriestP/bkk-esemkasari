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

    public static function getDashboardData()
    {
        // Data 7 bulan terakhir untuk chart
        $monthlyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            $vacanciesCount = Vacancy::where('created_by', auth('partner')->user()->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $applicationsCount = VacancyApplication::whereHas('vacancy', function ($query) {
                $query->where('created_by', auth('partner')->user()->id);
            })->whereBetween('created_at', [$monthStart, $monthEnd])->count();

            $monthlyStats[] = [
                'month' => $monthStart->format('Y-m-d'),
                'vacancies' => $vacanciesCount,
                'applications' => $applicationsCount
            ];
        }

        $dashboardData = [
            'totalVacancies' => Vacancy::where('created_by', auth('partner')->user()->id)->count(),
            'activeVacancies' => Vacancy::where('created_by', auth('partner')->user()->id)
                ->where('due_at', '>=', now())
                ->count(),
            'totalApplicants' => VacancyApplication::whereHas('vacancy', function ($query) {
                $query->where('created_by', auth('partner')->user()->id);
            })->count(),
            'totalQualifiedApplicants' => VacancyApplication::whereHas('vacancy', function ($query) {
                $query->where('created_by', auth('partner')->user()->id);
            })->where('status', 'qualified')->count(),
            'recentVacancies' => Vacancy::where('created_by', auth('partner')->user()->id)->latest()->take(5)->get(),
            'recentApplications' => VacancyApplication::with(['student', 'vacancy'])->whereHas('vacancy', function ($query) {
                $query->where('created_by', auth('partner')->user()->id);
            })->latest()->take(5)->get(),
            'applicationStats' => [
                'applied' => VacancyApplication::whereHas('vacancy', function ($query) {
                    $query->where('created_by', auth('partner')->user()->id);
                })->where('status', 'applied')->count(),
                'qualified' => VacancyApplication::whereHas('vacancy', function ($query) {
                    $query->where('created_by', auth('partner')->user()->id);
                })->where('status', 'qualified')->count(),
            ],
            'monthlyStats' => $monthlyStats,
        ];

        return $dashboardData;
    }
}
