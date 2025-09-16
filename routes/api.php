<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::get('', [ProfileController::class, 'index']);
        Route::post('', [ProfileController::class, 'store']);
        Route::get('/{id}', [ProfileController::class, 'show']);
        Route::put('/{id}', [ProfileController::class, 'update']);
    });


    Route::get('user', [UserController::class, 'GetUser']);
    Route::get('user/{id}/profile', [UserController::class, 'getProfile']);
    Route::get('user/{id}/tasks', [UserController::class, 'getUserTasks']);

    Route::apiResource('tasks', TaskController::class);
    Route::get('task/all', [TaskController::class, 'getAllTasks'])->middleware('checkUser');

    Route::get('task/ordered', [TaskController::class, 'getTaskByPriority']);

    Route::get('task/{id}/user', [TaskController::class, 'getTaskUser']);
    Route::post('tasks/{taskId}/categories', [TaskController::class, 'addCategoriesToTask']);
    Route::get('tasks/{taskId}/categories', [TaskController::class, 'getTaskCategories']);
    Route::get('categories/{categoryId}/tasks', [CategoryController::class, 'getCategoryTasks']);

    Route::post('task/{id}/favorite', [TaskController::class, 'addToFavorite']);
    Route::delete('task/{id}/favorite', [TaskController::class, 'removeFromFavorite']);
    Route::get('task/favorite', [TaskController::class, 'getFavoriteTasks']);
});
