<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::apiResource('todos', TodoController::class);
    Route::post('/todos/{todo}/invite', [TodoController::class, 'invite']);
    Route::post('/todos/{todo}/items', [TodoController::class, 'addItem']);
    Route::get('/users/search', [UserController::class, 'search']);
});
