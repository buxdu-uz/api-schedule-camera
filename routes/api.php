<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Buildings\BuildingController;
use App\Http\Controllers\Cameras\CameraController;
use App\Http\Controllers\Departments\DepartmentController;
use App\Http\Controllers\Groups\GroupController;
use App\Http\Controllers\Schedules\ScheduleListController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [LoginController::class, 'login']);
Route::get('/buildings',[BuildingController::class,'index']);
Route::post('/room/set/camera',[BuildingController::class,'setRoomCamera']);
Route::post('/groups',[GroupController::class,'getAllGroup']);
Route::get('departments', [DepartmentController::class, 'getAll']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum','role:admin']], function () {
    Route::apiResource('cameras', CameraController::class);
    Route::post('/camera/import', [CameraController::class, 'importExel']);
    Route::get('users', [UserController::class, 'paginate']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:teacher|employee|admin|manager']], function () {
    Route::get('/schedule/list',[ScheduleListController::class,'getScheduleListHemis']);
    Route::post('/update/login', [LoginController::class, 'updateLoginPassword']);
    Route::get('teachers',[UserController::class,'getAllTeacher']);
});

Route::group(['prefix' => 'teacher', 'middleware' => ['auth:sanctum','role:teacher']], function () {
    Route::get('schedule/list',[ScheduleListController::class,'teacherSchedule']);
});

