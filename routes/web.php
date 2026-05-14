<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect('/tasks');
});
Route::get('/tasks', [TaskController::class, 'index']);

Route::post('/tasks', [TaskController::class, 'store']);

Route::put('/tasks/{task}', [TaskController::class, 'update']);

Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

Route::patch('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete']);