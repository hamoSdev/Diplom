<?php

use App\Http\Controllers\CourseExamController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LecturerDirectoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('my-learning', [LearningController::class, 'index'])->name('learning.index');
    Route::get('my-learning/courses/{course}', [LearningController::class, 'show'])->name('learning.courses.show');

    Route::get('explore', [ExploreController::class, 'index'])->name('explore.index');
    Route::get('lecturers', [LecturerDirectoryController::class, 'index'])->name('lecturers.index');

    Route::get('my-learning/courses/{course}/exam', [CourseExamController::class, 'show'])->name('course-exam.show');
    Route::post('my-learning/courses/{course}/exam/start', [CourseExamController::class, 'start'])->name('course-exam.start');
    Route::get('my-learning/courses/{course}/exam/take', [CourseExamController::class, 'take'])->name('course-exam.take');
    Route::post('my-learning/courses/{course}/exam/answer', [CourseExamController::class, 'answer'])->name('course-exam.answer');
    Route::post('my-learning/courses/{course}/exam/skip', [CourseExamController::class, 'skip'])->name('course-exam.skip');
    Route::get('my-learning/courses/{course}/exam/result/{attempt}', [CourseExamController::class, 'result'])->name('course-exam.result');

    Route::get('exam', [ExamController::class, 'show'])->name('exam.show');
    Route::post('exam/start', [ExamController::class, 'start'])->name('exam.start');
    Route::get('exam/take', [ExamController::class, 'take'])->name('exam.take');
    Route::post('exam/answer', [ExamController::class, 'answer'])->name('exam.answer');
    Route::post('exam/skip', [ExamController::class, 'skip'])->name('exam.skip');
    Route::get('exam/result/{attempt}', [ExamController::class, 'result'])->name('exam.result');
});
