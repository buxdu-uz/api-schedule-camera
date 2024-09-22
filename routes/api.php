<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Buildings\BuildingController;
use App\Http\Controllers\Cameras\CameraController;
use App\Http\Controllers\Departments\DepartmentController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LoginController::class, 'login']);
Route::post('/update/login/{user}', [LoginController::class, 'updateLoginPassword']);
Route::get('/buildings',[BuildingController::class,'index']);


Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum','role:admin']], function () {
    Route::get('users', [UserController::class, 'paginate']);
    Route::get('departments', [DepartmentController::class, 'getAll']);
    Route::apiResource('cameras', CameraController::class);
});

Route::group(['prefix' => 'employee', 'middleware' => ['auth:sanctum', 'role:teacher']], function () {

});
