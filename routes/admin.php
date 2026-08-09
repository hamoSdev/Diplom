<?php

use App\Http\Controllers\Admin\AiMonitorController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LecturerApplicationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('faculties', [FacultyController::class, 'index'])->name('faculties.index');
    Route::post('faculties', [FacultyController::class, 'store'])->name('faculties.store');
    Route::put('faculties/{faculty}', [FacultyController::class, 'update'])->name('faculties.update');
    Route::delete('faculties/{faculty}', [FacultyController::class, 'destroy'])->name('faculties.destroy');

    Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::put('subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('ai', [AiMonitorController::class, 'index'])->name('ai.index');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('applications', [LecturerApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}/cv', [LecturerApplicationController::class, 'download'])->name('applications.cv');
    Route::put('applications/{application}', [LecturerApplicationController::class, 'update'])->name('applications.update');
    Route::delete('applications/{application}', [LecturerApplicationController::class, 'destroy'])->name('applications.destroy');
});
