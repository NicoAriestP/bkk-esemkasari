<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\StudentClassController;
use App\Http\Controllers\StudentController;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Announcement
Route::middleware(['auth'])->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{model}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{model}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{model}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Student Class
    Route::get('/student-classes', [StudentClassController::class, 'index'])->name('student-classes.index');
    Route::get('/student-classes/create', [StudentClassController::class, 'create'])->name('student-classes.create');
    Route::post('/student-classes', [StudentClassController::class, 'store'])->name('student-classes.store');
    Route::get('/student-classes/{model}/edit', [StudentClassController::class, 'edit'])->name('student-classes.edit');
    Route::put('/student-classes/{model}', [StudentClassController::class, 'update'])->name('student-classes.update');
    Route::delete('/student-classes/{model}', [StudentClassController::class, 'destroy'])->name('student-classes.destroy');

    // Student
    Route::get('/student-classes/{model}/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{model}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{model}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{model}', [StudentController::class, 'destroy'])->name('students.destroy');
});
