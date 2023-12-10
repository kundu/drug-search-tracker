<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DrugSearchController;
use App\Http\Controllers\MedicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('medication')->controller(MedicationController::class)->group(function () {
        Route::post('/add', 'addDrug');
        Route::delete('/delete/{medicationId}', [MedicationController::class, 'deleteDrug']);
        Route::get('/', 'getUserDrugs');
    });
});

Route::get('/search/{name}', [DrugSearchController::class, 'search'])->middleware('throttle:20,1');;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
