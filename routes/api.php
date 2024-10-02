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
Route::get('departments', [DepartmentController::class, 'getAllFakultet']);
Route::get('departments/all', [DepartmentController::class, 'getAll']);
Route::get('users',[UserController::class,'getAllUser']);
Route::get('users/{department_id}',[UserController::class,'getAllDepartmentUser']);
Route::get('roles',[UserController::class,'getAllRoles']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum','role:admin']], function () {
    Route::apiResource('cameras', CameraController::class);
    Route::post('/camera/import', [CameraController::class, 'importExel']);
    Route::get('users', [UserController::class, 'paginate']);
    Route::post('/user/set/camera',[UserController::class,'setUserCamera']);
    Route::get('/user/cameras',[UserController::class,'userCamerasForAdmin']);
    Route::get('/export/buildings/rooms', [BuildingController::class, 'exportBuilding']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:teacher|employee|admin|manager']], function () {
    Route::get('/schedule/list',[ScheduleListController::class,'getScheduleListHemis']);
    Route::post('/update/login', [LoginController::class, 'updateLoginPassword']);
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/cameras',[UserController::class,'userCamera']);
    Route::get('schedule/list',[ScheduleListController::class,'userSchedule']);
});

