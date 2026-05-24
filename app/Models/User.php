<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    public static function getDashboardData(int $months = 1)
    {
        $months = max($months, 1);
        $days = $months * 30;
        $startDate = now()->subDays($days);
        $endDate = now();

        // Get all data within the range
        $announcements = Announcement::whereBetween('created_at', [$startDate, $endDate])->get();
        $questionnaires = Questionnaire::whereBetween('created_at', [$startDate, $endDate])->get();
        $responses = QuestionnaireResponse::whereBetween('submitted_at', [$startDate, $endDate])->get();
        $students = Student::whereBetween('created_at', [$startDate, $endDate])->get();
        $partners = Partner::whereBetween('created_at', [$startDate, $endDate])->get();

        // Group by month for display
        $monthlyStats = [];
        $monthsToShow = new \DateTime($startDate);
        while ($monthsToShow < new \DateTime($endDate)) {
            $monthStart = \Carbon\Carbon::instance($monthsToShow)->startOfMonth();
            $monthEnd = \Carbon\Carbon::instance($monthsToShow)->endOfMonth();

            $monthlyStats[] = [
                'month' => $monthStart->format('Y-m-d'),
                'announcements' => $announcements->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'questionnaires' => $questionnaires->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'responses' => $responses->whereBetween('submitted_at', [$monthStart, $monthEnd])->count(),
                'students' => $students->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'partners' => $partners->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];

            $monthsToShow->modify('first day of next month');
        }

        // Ensure current month is always included
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        if (!$monthlyStats || $monthlyStats[count($monthlyStats) - 1]['month'] !== $currentMonthStart->format('Y-m-d')) {
            $monthlyStats[] = [
                'month' => $currentMonthStart->format('Y-m-d'),
                'announcements' => $announcements->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'questionnaires' => $questionnaires->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'responses' => $responses->whereBetween('submitted_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'students' => $students->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'partners' => $partners->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            ];
        }

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
            'recentAnnouncements' => Announcement::with('createdBy')->latest()->take(5)->get(),
            'recentQuestionnaires' => Questionnaire::with('createdBy')->withCount('responses')->latest()->take(5)->get(),
            'recentTracerStudyStudents' => Student::with([
                'studentClass.year',
                'studentActivityAnswer',
                'studentUniversityAnswer',
                'studentWorkingAnswer',
                'studentEntrepreneurAnswer'
            ])->where(function ($query) {
                $query->whereHas('studentActivityAnswer')
                    ->orWhereHas('studentUniversityAnswer')
                    ->orWhereHas('studentWorkingAnswer')
                    ->orWhereHas('studentEntrepreneurAnswer');
            })->latest()->take(5)->get(),
            'recentPartners' => Partner::latest()->take(5)->get(),
            'monthlyStats' => $monthlyStats,
            'monthlyRange' => $months,
            'studentStatusStats' => [
                'graduatedStudents' => $graduatedStudents,
                'pendingGraduationStudents' => max($totalStudents - $graduatedStudents, 0),
                'tracerStudyCompletedStudents' => $tracerStudyCompletedStudents,
                'pendingTracerStudents' => $pendingTracerStudents,
            ],
        ];
    }
}
