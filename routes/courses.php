<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseMaterialController;
use App\Http\Controllers\LecturerResultsController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

// A lecturer's own course list and CRUD.
Route::middleware(['auth', 'verified', 'role:lecturer'])->group(function () {
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Student results across the lecturer's courses.
    Route::get('results', [LecturerResultsController::class, 'index'])->name('results.index');
    Route::get('results/courses/{course}', [LecturerResultsController::class, 'show'])->name('results.show');
    Route::get('results/attempts/{attempt}', [LecturerResultsController::class, 'attempt'])->name('results.attempt');
});

// Course structure. Course-scoped and authorized via CoursePolicy, so a lecturer
// reaches only their own courses while an admin (Gate::before) reaches any course.
Route::middleware(['auth', 'verified', 'role:lecturer,admin'])->group(function () {
    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('courses/{course}/generate', [CourseController::class, 'generate'])->name('courses.generate');

    Route::post('courses/{course}/materials', [CourseMaterialController::class, 'store'])->name('courses.materials.store');
    Route::post('courses/{course}/materials/generate', [CourseMaterialController::class, 'generate'])->name('courses.materials.generate');
    Route::delete('courses/{course}/materials/{material}', [CourseMaterialController::class, 'destroy'])->name('courses.materials.destroy');

    Route::post('courses/{course}/topics', [TopicController::class, 'store'])->name('courses.topics.store');
    Route::put('courses/{course}/topics/{topic}', [TopicController::class, 'update'])->name('courses.topics.update');
    Route::delete('courses/{course}/topics/{topic}', [TopicController::class, 'destroy'])->name('courses.topics.destroy');
    Route::post('courses/{course}/topics/{topic}/generate', [TopicController::class, 'generate'])->name('courses.topics.generate');

    Route::post('courses/{course}/questions', [QuestionController::class, 'store'])->name('courses.questions.store');
    Route::delete('courses/{course}/questions/{question}', [QuestionController::class, 'destroy'])->name('courses.questions.destroy');
});
