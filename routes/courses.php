<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:lecturer'])->group(function () {
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});
