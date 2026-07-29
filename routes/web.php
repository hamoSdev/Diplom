<?php

use App\Http\Controllers\CourseMaterialController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('ai-learning', 'landing/AiLearning')->name('ai-learning');
Route::inertia('our-courses', 'landing/OurCourses')->name('our-courses');
Route::inertia('faq', 'landing/Faq')->name('faq');
Route::inertia('become-lecturer', 'landing/BecomeLecturer')->name('become-lecturer');
Route::inertia('our-lecturers', 'landing/OurLecturers')->name('our-lecturers');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('materials/{material}/download', [CourseMaterialController::class, 'download'])->name('materials.download');
});

require __DIR__.'/admin.php';
require __DIR__.'/courses.php';
require __DIR__.'/editor.php';
require __DIR__.'/student.php';
require __DIR__.'/settings.php';
