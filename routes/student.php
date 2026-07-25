<?php

use App\Http\Controllers\LearningController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('my-learning', [LearningController::class, 'index'])->name('learning.index');
});
