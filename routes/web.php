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

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth:web,student,partner', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


Route::middleware(['auth:web,student,partner'])->group(function () {
    // Announcement
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{model}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{model}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{model}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Announcement (Student)
    Route::get('/announcements/student', [AnnouncementController::class, 'indexAnnouncementStudent'])->name('announcements.student.index');
    Route::get('/announcements/{model}', [AnnouncementController::class, 'detailAnnouncementStudent'])->name('announcements.student.detail');

    // Year
    Route::get('/years', [YearController::class, 'index'])->name('years.index');
    Route::post('/years', [YearController::class, 'store'])->name('years.store');
    Route::put('/years/{model}', [YearController::class, 'update'])->name('years.update');
    Route::delete('/years/{model}', [YearController::class, 'destroy'])->name('years.destroy');

    // Student Class
    Route::get('/years/{year}/student-classes', [StudentClassController::class, 'index'])->name('student-classes.index');
    Route::post('/years/{year}/student-classes', [StudentClassController::class, 'store'])->name('student-classes.store');
    Route::put('/years/{year}/student-classes/{model}', [StudentClassController::class, 'update'])->name('student-classes.update');
    Route::delete('/years/{year}/student-classes/{model}', [StudentClassController::class, 'destroy'])->name('student-classes.destroy');

    // Student
    Route::get('/years/{year}/student-classes/{studentClass}/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/years/{year}/student-classes/{studentClass}/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/years/{year}/student-classes/{studentClass}/students/{model}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/years/{year}/student-classes/{studentClass}/students/{model}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/years/{year}/student-classes/{studentClass}/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::post('/years/{year}/student-classes/{studentClass}/students/export', [StudentController::class, 'export'])->name('students.export');

    // Tracer Study
    Route::get('/tracer-study', [TracerStudyController::class, 'tracerStudy'])->name('tracer-study');
    Route::post('/tracer-study', [TracerStudyController::class, 'saveTracerStudy'])->name('tracer-study.store');

    // Partner
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
    Route::put('/partners/{model}', [PartnerController::class, 'update'])->name('partners.update');
    Route::delete('/partners/{model}', [PartnerController::class, 'destroy'])->name('partners.destroy');

    // Vacancy (Partner)
    Route::get('/partners/vacancies', [VacancyPartnerController::class, 'index'])->name('partners.vacancies.index');
    Route::get('/partners/vacancies/create', [VacancyPartnerController::class, 'create'])->name('partners.vacancies.create');
    Route::post('/partners/vacancies', [VacancyPartnerController::class, 'store'])->name('partners.vacancies.store');
    Route::get('/partners/vacancies/{model}/edit', [VacancyPartnerController::class, 'edit'])->name('partners.vacancies.edit');
    Route::put('/partners/vacancies/{model}', [VacancyPartnerController::class, 'update'])->name('partners.vacancies.update');
    Route::delete('/partners/vacancies/{model}', [VacancyPartnerController::class, 'destroy'])->name('partners.vacancies.destroy');
    Route::get('/partners/vacancies/{model}/applications', [VacancyPartnerController::class, 'applications'])->name('partners.vacancies.applications');
    Route::post('/partners/vacancies/{model}/applications', [VacancyPartnerController::class, 'saveApplications'])->name('partners.vacancies.applications.store');
    Route::get('/partners/vacancies/{model}/applications/export', [VacancyPartnerController::class, 'exportApplications'])->name('partners.vacancies.applications.export');

    // Vacancy (Student)
    Route::get('/students/vacancies', [VacancyStudentController::class, 'index'])->name('students.vacancies.index');
    Route::get('/students/vacancies/{model}', [VacancyStudentController::class, 'show'])->name('students.vacancies.show');
    Route::post('/students/vacancies/{model}', [VacancyStudentController::class, 'applyVacancy'])->name('students.vacancies.apply');

    // User
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{model}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{model}', [UserController::class, 'destroy'])->name('users.destroy');
});
