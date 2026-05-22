<?php

use App\Http\Controllers\Student\AssessmentController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\MaterialController;
use App\Http\Controllers\Student\ResumeController;
use App\Http\Controllers\Student\TokenController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// ─── Rute Siswa ───────────────────────────────────────────────
Route::prefix('belajar/{slug}')->name('student.')->group(function () {

    // Landing page course (form join / lanjutkan sesi)
    Route::get('/', [CourseController::class, 'show'])->name('course.show');
    Route::post('/join', [CourseController::class, 'join'])->name('course.join');
    Route::post('/lanjutkan', [CourseController::class, 'resume'])->name('course.resume');

    // Materi
    Route::get('/materi/{materialId}', [MaterialController::class, 'show'])->name('material.show');

    // Resume
    Route::get('/materi/{materialId}/resume', [ResumeController::class, 'create'])->name('resume.create');
    Route::post('/materi/{materialId}/resume', [ResumeController::class, 'store'])->name('resume.store');

    // Token gate
    Route::get('/token/{type}/{materialId}', [TokenController::class, 'gate'])->name('token.gate');
    Route::post('/token/{type}/{materialId}', [TokenController::class, 'verify'])->name('token.verify');

    // Asesmen
    Route::get('/asesmen/{assessmentId}', [AssessmentController::class, 'show'])->name('assessment.show');
    Route::post('/asesmen/{assessmentId}', [AssessmentController::class, 'store'])->name('assessment.store');
});
