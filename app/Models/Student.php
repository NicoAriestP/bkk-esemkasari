<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasFeaturedFile;
use App\Enum\VacancyApplicationStatus;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
        'cv_file_name',
        'age',
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

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->born_date ? Carbon::parse($this->born_date)->age : null,
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

    public function cvFileName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->cv_file
                ? basename($this->cv_file)
                : null,
        );
    }

    public static function getDashboardData()
    {
        /** @var Student $student */
        $student = auth('student')->user();

        // Total applications
        $totalApplications = $student->vacancyApplication()->count();
        $pendingApplications = $student->vacancyApplication()
            ->where('status', VacancyApplicationStatus::APPLIED->value)
            ->count();
        $qualifiedApplications = $student->vacancyApplication()
            ->where('status', VacancyApplicationStatus::QUALIFIED->value)
            ->count();
        $rejectedApplications = $totalApplications - $pendingApplications - $qualifiedApplications;

        // Recent vacancies - limit to 5 (future vacancies)
        $recentVacancies = Vacancy::with('createdBy')
            ->where('due_at', '>=', now())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Student's recent applications - limit to 5
        $myApplications = $student->vacancyApplication()
            ->with(['vacancy' => function ($query) {
                $query->with('createdBy')
                    ->select('id', 'title', 'location', 'created_by', 'due_at');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'status' => $application->status,
                    'created_at' => $application->created_at,
                    'vacancy' => [
                        'id' => $application->vacancy->id,
                        'title' => $application->vacancy->title,
                        'location' => $application->vacancy->location,
                        'due_at' => $application->vacancy->due_at,
                        'created_by' => $application->vacancy->createdBy ? [
                            'id' => $application->vacancy->createdBy->id,
                            'name' => $application->vacancy->createdBy->name,
                        ] : null,
                    ]
                ];
            });

        // Recent announcements - limit to 5
        $announcements = Announcement::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'excerpt' => $announcement->excerpt,
                    'created_at' => $announcement->created_at,
                ];
            });

        // Tracer study status
        $tracerStudyCompleted = $student->studentActivityAnswer()->exists() ||
            $student->studentUniversityAnswer()->exists() ||
            $student->studentWorkingAnswer()->exists() ||
            $student->studentEntrepreneurAnswer()->exists();

        $tracerStudyStatus = [
            'completed' => $tracerStudyCompleted,
            'completed_at' => null,
        ];

        // Get the latest completed_at from tracer study answers
        if ($tracerStudyCompleted) {
            $completedTimes = [
                optional($student->studentActivityAnswer)->updated_at,
                optional($student->studentUniversityAnswer)->updated_at,
                optional($student->studentWorkingAnswer)->updated_at,
                optional($student->studentEntrepreneurAnswer)->updated_at,
            ];
            $completedTimes = array_filter($completedTimes);
            if (!empty($completedTimes)) {
                $tracerStudyStatus['completed_at'] = max(array_map(fn($date) => strtotime((string)$date), $completedTimes));
                $tracerStudyStatus['completed_at'] = Carbon::createFromTimestamp($tracerStudyStatus['completed_at']);
            }
        }

        // Application stats by month (last 6 months for better trend visualization)
        $applicationStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            $count = $student->vacancyApplication()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $applicationStats[] = [
                'month' => $monthStart->format('Y-m-d'),
                'count' => $count,
            ];
        }

        // Count applicants per vacancy (for badge in recentVacancies)
        $recentVacancies = $recentVacancies->map(function ($vacancy) {
            return [
                'id' => $vacancy->id,
                'title' => $vacancy->title,
                'location' => $vacancy->location,
                'due_at' => $vacancy->due_at,
                'created_by' => $vacancy->createdBy ? [
                    'id' => $vacancy->createdBy->id,
                    'name' => $vacancy->createdBy->name,
                ] : null,
                'applicants_count' => $vacancy->vacancyApplication()->count(),
            ];
        });

        return [
            'totalApplications' => $totalApplications,
            'pendingApplications' => $pendingApplications,
            'qualifiedApplications' => $qualifiedApplications,
            'rejectedApplications' => $rejectedApplications,
            'recentVacancies' => $recentVacancies,
            'myApplications' => $myApplications,
            'announcements' => $announcements,
            'tracerStudyStatus' => $tracerStudyStatus,
            'applicationStats' => $applicationStats,
        ];
    }

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function studentActivityAnswer()
    {
        return $this->hasOne(StudentActivityAnswer::class);
    }

    public function detailActivityAnswer()
    {
        return $this->hasOne(DetailActivityAnswer::class);
    }

    public function studentUniversityAnswer()
    {
        return $this->hasOne(StudentUniversityAnswer::class);
    }

    public function studentWorkingAnswer()
    {
        return $this->hasOne(StudentWorkingAnswer::class);
    }

    public function studentEntrepreneurAnswer()
    {
        return $this->hasOne(StudentEntrepreneurAnswer::class);
    }

    public function feedbackAnswer()
    {
        return $this->hasOne(FeedbackAnswer::class);
    }

    public function vacancyApplication()
    {
        return $this->hasMany(VacancyApplication::class);
    }

    public function responses()
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }
}
