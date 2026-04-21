<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;

// All these routes are automatically prefixed with /api/
Route::get('/expenses', [ExpenseController::class, 'index']);
Route::post('/expenses', [ExpenseController::class, 'store']);
Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
