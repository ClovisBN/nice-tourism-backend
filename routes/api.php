<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\PointOfInterestController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

// Route protégée pour rafraîchir le token
Route::middleware(['auth:sanctum', 'abilities:issue-access-token'])->post('auth/refresh-token', [AuthController::class, 'refreshToken']);
Route::apiResource('points-of-interest', PointOfInterestController::class);

Route::middleware(['auth:sanctum', 'abilities:access-api'])->group(function () {
    Route::get('users', [AuthController::class, 'listUsers']);
    Route::apiResource('articles', ArticleController::class);
    Route::post('/save-document', [DocumentController::class, 'store']);
    Route::get('/documents/{id}', [DocumentController::class, 'show']);
});
