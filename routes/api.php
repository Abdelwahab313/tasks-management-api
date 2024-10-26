<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;





Route::prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::put('/', [TaskController::class, 'update']);
    Route::delete('/', [TaskController::class, 'destroy']);
});
