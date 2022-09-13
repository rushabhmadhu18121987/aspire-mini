<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LoanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('apply-loan',[LoanController::class,'applyLoan']);
    Route::post('loan-emis',[LoanController::class,'loanEmis']);
    Route::post('pay-emi',[LoanController::class,'store']);
});

Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('loans', [LoanController::class,'index']);
    Route::put('approve-loan/{id}', [LoanController::class,'approveLoan']);
});
