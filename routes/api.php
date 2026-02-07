<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllerlar
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\ResultController;

/*
|--------------------------------------------------------------------------
| Ochiq Marshrutlar (Public)
|--------------------------------------------------------------------------
*/
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/test-access/{unique_link}', [TestController::class, 'getByLink']);

/*
|--------------------------------------------------------------------------
| Himoyalangan Marshrutlar (Auth Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Umumiy foydalanuvchi ma'lumotlari
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // --- STUDENT (Test topshirish jarayoni) ---
    Route::post('/start-test', [SubmissionController::class, 'start']);
    Route::post('/submit-test', [SubmissionController::class, 'store']);
    Route::get('/my-results', [ResultController::class, 'myResults']);

    // --- ADMIN (Boshqaruv va Analitika) ---
    // Dashboard va Statistika
    Route::get('/admin/dashboard-stats', [DashboardController::class, 'index']);

    // Natijalarni ko'rish
    Route::get('/admin/results', [ResultController::class, 'index']);
    Route::get('/admin/results/{id}', [ResultController::class, 'show']);

    // Resurslar (CRUD)
    Route::apiResource('users', UserController::class);
    Route::apiResource('tests', TestController::class);
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('questions', QuestionController::class);
});
