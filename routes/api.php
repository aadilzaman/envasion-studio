<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Dashboard summary
Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

// Projects CRUD
Route::apiResource('projects', ProjectController::class);

// Project-nested tasks
Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);

// Tasks CRUD
Route::apiResource('tasks', TaskController::class);
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle']);
