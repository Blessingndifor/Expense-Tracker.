<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController; // 👈 You must add this line!

// This loads the welcome page
Route::get('/', function () {
    return view('welcome');
});

// Load the list using the Controller (this fetches the data)
Route::get('/expenses', [ExpenseController::class, 'index']);

// Handle the form submission
Route::post('/expenses', [ExpenseController::class, 'store']);

// Handle the delete button
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

Route::put('/expenses/{id}', [ExpenseController::class, 'update']);

