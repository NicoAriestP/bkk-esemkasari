<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\VacancyPartnerController;
use App\Http\Controllers\VacancyStudentController;
use App\Http\Controllers\TracerStudyController;
use App\Http\Controllers\DashboardPartnerController;
use App\Http\Controllers\VacancyApplicationController;
use App\Http\Controllers\HomePageController;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Home Page Routes
Route::controller(HomePageController::class)->group(function () {
    Route::get('/', 'index')->name('home');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth:web,student,partner', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth:web'])->group(function () {
    // Announcement Routes
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/{model}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{model}', [AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{model}', [AnnouncementController::class, 'destroy'])->name('destroy');
    });

    // Year Routes
    Route::prefix('years')->name('years.')->group(function () {
        Route::get('/', [YearController::class, 'index'])->name('index');
        Route::post('/', [YearController::class, 'store'])->name('store');
        Route::put('/{model}', [YearController::class, 'update'])->name('update');
        Route::delete('/{model}', [YearController::class, 'destroy'])->name('destroy');

        // Student Class Routes (nested under years)
        Route::prefix('{year}/student-classes')->name('student-classes.')->group(function () {
            Route::get('/', [StudentClassController::class, 'index'])->name('index');
            Route::post('/', [StudentClassController::class, 'store'])->name('store');
            Route::put('/{model}', [StudentClassController::class, 'update'])->name('update');
            Route::delete('/{model}', [StudentClassController::class, 'destroy'])->name('destroy');

            // Student Routes (nested under student-classes)
            Route::prefix('{studentClass}/students')->name('students.')->group(function () {
                Route::get('/', [StudentController::class, 'index'])->name('index');
                Route::post('/', [StudentController::class, 'store'])->name('store');
                Route::put('/{model}', [StudentController::class, 'update'])->name('update');
                Route::delete('/{model}', [StudentController::class, 'destroy'])->name('destroy');
                Route::post('/import', [StudentController::class, 'import'])->name('import');
                Route::post('/export', [StudentController::class, 'export'])->name('export');
            });
        });
    });

    // Partner Management Routes
    Route::prefix('partners')->name('partners.')->group(function () {
        Route::get('/', [PartnerController::class, 'index'])->name('index');
        Route::post('/', [PartnerController::class, 'store'])->name('store');
        Route::put('/{model}', [PartnerController::class, 'update'])->name('update');
        Route::delete('/{model}', [PartnerController::class, 'destroy'])->name('destroy');
    });

    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{model}', [UserController::class, 'update'])->name('update');
        Route::delete('/{model}', [UserController::class, 'destroy'])->name('destroy');
    });
});

// Student Routes
Route::middleware('auth:student')->group(function () {
    Route::prefix('students/dashboard')->name('students.dashboard')->group(function () {
        Route::get('/', [StudentController::class, 'dashboard']);
    });
    // Announcement Routes (Student)
    Route::prefix('students/announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'indexAnnouncementStudent'])->name('student.index');
        Route::get('/{model}', [AnnouncementController::class, 'detailAnnouncementStudent'])->name('student.detail');
    });

    // Tracer Study Routes
    Route::prefix('tracer-study')->name('tracer-study')->group(function () {
        Route::get('/', [TracerStudyController::class, 'tracerStudy']);
        Route::post('/', [TracerStudyController::class, 'saveTracerStudy'])->name('.store');
    });

    // Vacancy Routes (Student)
    Route::prefix('students/vacancies')->name('students.vacancies.')->group(function () {
        Route::get('/', [VacancyStudentController::class, 'index'])->name('index');
        Route::get('/{model}', [VacancyStudentController::class, 'show'])->name('show');
        Route::post('/{model}', [VacancyStudentController::class, 'applyVacancy'])->name('apply');
    });
});

// Partner Routes
Route::middleware('auth:partner')->group(function () {
    Route::prefix('partners/dashboard')->name('partners.dashboard')->group(function () {
        Route::get('/', [PartnerController::class, 'dashboard']);
    });

    // Vacancy Routes (Partner)
    Route::prefix('partners/vacancies')->name('partners.vacancies.')->group(function () {
        Route::get('/', [VacancyPartnerController::class, 'index'])->name('index');
        Route::get('/create', [VacancyPartnerController::class, 'create'])->name('create');
        Route::post('/', [VacancyPartnerController::class, 'store'])->name('store');
        Route::get('/{model}/edit', [VacancyPartnerController::class, 'edit'])->name('edit');
        Route::put('/{model}', [VacancyPartnerController::class, 'update'])->name('update');
        Route::delete('/{model}', [VacancyPartnerController::class, 'destroy'])->name('destroy');

        // Vacancy Applications Routes (nested under vacancies)
        Route::prefix('{model}/applications')->name('applications')->group(function () {
            Route::get('/', [VacancyPartnerController::class, 'applications']);
            Route::post('/', [VacancyPartnerController::class, 'saveApplications'])->name('.store');
            Route::get('/export', [VacancyPartnerController::class, 'exportApplications'])->name('.export');
        });
    });
});
