<?php

use App\Http\Controllers\EditorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:lecturer,admin'])->group(function () {
    Route::get('editor', [EditorController::class, 'index'])->name('editor.index');
    Route::post('editor/save', [EditorController::class, 'store'])->name('editor.store');
});
