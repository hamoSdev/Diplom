<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:lecturer,admin'])->group(function () {
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
});
