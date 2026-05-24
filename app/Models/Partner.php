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

    public static function getDashboardData(int $months = 1)
    {
        $months = max($months, 1);
        $days = $months * 30;
        $startDate = now()->subDays($days);
        $endDate = now();

        // Get all data within the range for current partner
        $vacancies = Vacancy::where('created_by', auth('partner')->user()->id)
            ->whereBetween('created_at', [$startDate, $endDate])->get();

        $applications = VacancyApplication::whereHas('vacancy', function ($query) {
            $query->where('created_by', auth('partner')->user()->id);
        })->whereBetween('created_at', [$startDate, $endDate])->get();

        // Group by month for display
        $monthlyStats = [];
        $monthsToShow = new \DateTime($startDate);
        while ($monthsToShow < new \DateTime($endDate)) {
            $monthStart = \Carbon\Carbon::instance($monthsToShow)->startOfMonth();
            $monthEnd = \Carbon\Carbon::instance($monthsToShow)->endOfMonth();

            $monthlyStats[] = [
                'month' => $monthStart->format('Y-m-d'),
                'vacancies' => $vacancies->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'applications' => $applications->whereBetween('created_at', [$monthStart, $monthEnd])->count()
            ];

            $monthsToShow->modify('first day of next month');
        }

        // Ensure current month is always included
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        if (!$monthlyStats || $monthlyStats[count($monthlyStats) - 1]['month'] !== $currentMonthStart->format('Y-m-d')) {
            $monthlyStats[] = [
                'month' => $currentMonthStart->format('Y-m-d'),
                'vacancies' => $vacancies->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'applications' => $applications->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count()
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
            'monthlyRange' => $months,
        ];

        return $dashboardData;
    }
}
