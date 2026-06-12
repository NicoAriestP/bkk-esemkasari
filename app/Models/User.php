<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\TracerStudy\DetailActivityMainOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'phone',
        'email',
        'password',
        'is_leader'
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

    private static function buildMonthlySeries(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate, callable $resolver): array
    {
        $stats = [];
        $monthsToShow = new \DateTime($startDate);

        while ($monthsToShow < new \DateTime($endDate)) {
            $monthStart = \Carbon\Carbon::instance($monthsToShow)->startOfMonth();
            $monthEnd = \Carbon\Carbon::instance($monthsToShow)->endOfMonth();

            $stats[] = array_merge([
                'month' => $monthStart->format('Y-m-d'),
            ], $resolver($monthStart, $monthEnd));

            $monthsToShow->modify('first day of next month');
        }

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        if (!$stats || $stats[count($stats) - 1]['month'] !== $currentMonthStart->format('Y-m-d')) {
            $stats[] = array_merge([
                'month' => $currentMonthStart->format('Y-m-d'),
            ], $resolver($currentMonthStart, $currentMonthEnd));
        }

        return $stats;
    }

    public static function getDashboardData(int $months = 1, int $detailMonths = 1)
    {
        $months = max($months, 1);
        $detailMonths = max($detailMonths, 1);
        $days = $months * 30;
        $startDate = now()->subDays($days);
        $endDate = now();
        $detailDays = $detailMonths * 30;
        $detailStartDate = now()->subDays($detailDays);
        $detailEndDate = now();

        // Group by month for display
        $monthlyStats = self::buildMonthlySeries($startDate, $endDate, function (\Carbon\Carbon $monthStart, \Carbon\Carbon $monthEnd) {
            return [
                'announcements' => Announcement::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'questionnaires' => Questionnaire::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'responses' => QuestionnaireResponse::whereBetween('submitted_at', [$monthStart, $monthEnd])->count(),
                'students' => Student::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'partners' => Partner::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        });

        $detailActivityMonthlyStats = self::buildMonthlySeries($detailStartDate, $detailEndDate, function (\Carbon\Carbon $monthStart, \Carbon\Carbon $monthEnd) {
            return [
                'working' => DetailActivityAnswer::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->whereJsonContains('answers->mainActivity', DetailActivityMainOption::WORKING->value)
                    ->count(),
                'university' => DetailActivityAnswer::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->whereJsonContains('answers->mainActivity', DetailActivityMainOption::UNIVERSITY->value)
                    ->count(),
                'entrepreneur' => DetailActivityAnswer::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->whereJsonContains('answers->mainActivity', DetailActivityMainOption::ENTREPRENEUR->value)
                    ->count(),
                'notYet' => DetailActivityAnswer::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->whereJsonContains('answers->mainActivity', DetailActivityMainOption::NOT_YET->value)
                    ->count(),
            ];
        });

        $totalStudents = Student::count();
        $graduatedStudents = Student::where('is_graduated', true)->count();

        $tracerStudyCompletedStudents = Student::where(function ($query) {
            $query->whereHas('studentActivityAnswer')
                ->orWhereHas('studentUniversityAnswer')
                ->orWhereHas('studentWorkingAnswer')
                ->orWhereHas('studentEntrepreneurAnswer');
        })->count();

        $pendingTracerStudents = max($totalStudents - $tracerStudyCompletedStudents, 0);

        return [
            'totalAnnouncements' => Announcement::count(),
            'announcementsThisMonth' => Announcement::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'totalQuestionnaires' => Questionnaire::count(),
            'activeQuestionnaires' => Questionnaire::where(function ($query) {
                $query->whereNull('due_at')
                    ->orWhere('due_at', '>=', now());
            })->count(),
            'expiredQuestionnaires' => Questionnaire::whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
            'questionnaireResponses' => QuestionnaireResponse::count(),
            'totalStudents' => $totalStudents,
            'graduatedStudents' => $graduatedStudents,
            'tracerStudyCompletedStudents' => $tracerStudyCompletedStudents,
            'pendingTracerStudents' => $pendingTracerStudents,
            'totalPartners' => Partner::count(),
            'recentAnnouncements' => Announcement::query()
                ->select(['id', 'title', 'created_by', 'created_at'])
                ->with(['createdBy:id,name'])
                ->latest()
                ->take(5)
                ->get(),
            'recentQuestionnaires' => Questionnaire::query()
                ->select(['id', 'title', 'created_by', 'due_at', 'created_at'])
                ->with(['createdBy:id,name'])
                ->withCount('responses')
                ->latest()
                ->take(5)
                ->get(),
            'recentTracerStudyStudents' => Student::query()
                ->select(['id', 'name', 'nisn', 'student_class_id', 'is_graduated'])
                ->with([
                    'studentClass:id,year_id,name',
                    'studentClass.year:id,year',
                ])
                ->where(function ($query) {
                $query->whereHas('studentActivityAnswer')
                    ->orWhereHas('studentUniversityAnswer')
                    ->orWhereHas('studentWorkingAnswer')
                    ->orWhereHas('studentEntrepreneurAnswer');
                })
                ->latest()
                ->take(5)
                ->get(),
            'recentPartners' => Partner::query()
                ->select(['id', 'name', 'email', 'phone', 'address', 'created_at'])
                ->latest()
                ->take(5)
                ->get(),
            'monthlyStats' => $monthlyStats,
            'detailActivityMonthlyStats' => $detailActivityMonthlyStats,
            'monthlyRange' => $months,
            'detailMonthlyRange' => $detailMonths,
            'studentStatusStats' => [
                'graduatedStudents' => $graduatedStudents,
                'pendingGraduationStudents' => max($totalStudents - $graduatedStudents, 0),
                'tracerStudyCompletedStudents' => $tracerStudyCompletedStudents,
                'pendingTracerStudents' => $pendingTracerStudents,
            ],
        ];
    }
}
