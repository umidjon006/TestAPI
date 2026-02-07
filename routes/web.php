<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminWebController;
use App\Http\Controllers\Admin\DashboardWebController;
use App\Http\Controllers\Admin\SectionWebController;
use App\Http\Controllers\Admin\QuestionWebController;
use App\Http\Controllers\Admin\ResultWebController;
use App\Http\Controllers\Admin\AnalyticsWebController;
use App\Http\Controllers\Student\StudentExamController;

// --- ASOSIY SAHIFA ---
Route::get('/', function () {
    // Agar foydalanuvchi tizimga kirgan bo'lsa -> Admin Dashboardga o'tsin
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    // Agar kirmagan bo'lsa -> Login sahifasiga o'tsin
    return redirect()->route('login');
});

// --- 1. MEHMONLAR (Login sahifasi) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminWebController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminWebController::class, 'login'])->name('admin.login.submit');
});

// --- 2. ADMIN PANEL (Himoyalangan qism) ---
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // --- DASHBOARD (TESTLAR) ---
    Route::get('/', [DashboardWebController::class, 'index'])->name('dashboard');
    Route::post('/tests', [DashboardWebController::class, 'store'])->name('tests.store');
    Route::put('/tests/{id}', [DashboardWebController::class, 'update'])->name('tests.update');
    Route::delete('/tests/{id}', [DashboardWebController::class, 'destroy'])->name('tests.destroy');

    // --- BO'LIMLAR (SECTIONS) ---
    Route::get('/tests/{test_id}/sections', [SectionWebController::class, 'index'])->name('sections.index');
    Route::post('/tests/{test_id}/sections', [SectionWebController::class, 'store'])->name('sections.store');
    Route::put('/sections/{id}', [SectionWebController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{id}', [SectionWebController::class, 'destroy'])->name('sections.destroy');

    // --- SAVOLLAR (QUESTIONS) ---
    Route::get('/sections/{section_id}/questions', [QuestionWebController::class, 'index'])->name('questions.index');
    Route::post('/sections/{section_id}/questions', [QuestionWebController::class, 'store'])->name('questions.store');
    Route::put('/questions/{id}', [QuestionWebController::class, 'update'])->name('questions.update'); // Tahrirlash
    Route::delete('/questions/{id}', [QuestionWebController::class, 'destroy'])->name('questions.destroy');

    // --- NATIJALAR (RESULTS) ---
    Route::get('/results', [ResultWebController::class, 'index'])->name('results.index');
    Route::delete('/results/{id}', [ResultWebController::class, 'destroy'])->name('results.destroy');

    // --- ANALITIKA ---
    Route::get('/analytics', [AnalyticsWebController::class, 'index'])->name('analytics.index');

    // --- TIZIMDAN CHIQISH ---
    Route::post('/logout', [AdminWebController::class, 'logout'])->name('logout');
});

// --- STUDENT PANEL ---
Route::get('/exam/{unique_link}', [StudentExamController::class, 'showRegister'])->name('student.register');
Route::post('/exam/{unique_link}/start', [StudentExamController::class, 'startExam'])->name('student.start');
Route::get('/exam/{unique_link}/test', [StudentExamController::class, 'showTest'])->name('student.test');
Route::post('/exam/{unique_link}/submit', [StudentExamController::class, 'submitTest'])->name('student.submit');
Route::get('/result/{result_id}', [StudentExamController::class, 'showResult'])->name('student.result');
