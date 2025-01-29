<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Buildings\BuildingController;
use App\Http\Controllers\Cameras\CameraController;
use App\Http\Controllers\Departments\DepartmentController;
use App\Http\Controllers\Favourites\FavouriteController;
use App\Http\Controllers\Generations\GenerationController;
use App\Http\Controllers\Groups\GroupController;
use App\Http\Controllers\Schedules\ScheduleListController;
use App\Http\Controllers\SubjectGroups\SubjectGroupController;
use App\Http\Controllers\Subjects\SubjectController;
use App\Http\Controllers\Syllabus\SyllabusController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/redirect', [OAuthController::class, 'redirectToProvider'])->middleware('web');
Route::get('/callback', [OAuthController::class, 'handleProviderCallback'])->middleware('web');
Route::post('/login', [LoginController::class, 'login']);
Route::get('favourites',[FavouriteController::class,'getAll']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum','role:admin']], function () {
    Route::post('/room/set/camera',[BuildingController::class,'setRoomCamera']);
    Route::get('roles',[UserController::class,'getAllRoles']);
    Route::apiResource('cameras', CameraController::class);
    Route::post('camera/import', [CameraController::class, 'importExel']);
    Route::get('users', [UserController::class, 'paginate']);
    Route::post('user/set/camera',[UserController::class,'setUserCamera']);
    Route::get('user/cameras',[UserController::class,'userCamerasForAdmin']);
    Route::get('export/buildings/rooms', [BuildingController::class, 'exportBuilding']);
    Route::get('subjects',[SubjectController::class,'paginate']);
    Route::post('syllabus',[SyllabusController::class,'store']);
    Route::put('syllabus/{syllabus}',[SyllabusController::class,'update']);
    Route::get('subject_groups',[SubjectGroupController::class,'index']);
    Route::get('camera/favourites',[CameraController::class,'getAllFavouriteCameras']);

    Route::post('camera/{favourite}/favourite/attach',[FavouriteController::class,'attachCameraToFavourite']);
    Route::get('favourites',[FavouriteController::class,'index']);
    Route::post('favourites',[FavouriteController::class,'store']);
    Route::put('favourites/{favourite}',[FavouriteController::class,'update']);
    Route::delete('favourites/{favourite}',[FavouriteController::class,'destroy']);
});

//public
    Route::get('/schedule/list',[ScheduleListController::class,'getScheduleListHemis']);
    Route::get('buildings',[BuildingController::class,'index']);
    Route::get('departments', [DepartmentController::class, 'getAllFakultet']);
    Route::post('/groups',[GroupController::class,'index']);
    Route::get('departments/all', [DepartmentController::class, 'getAll']);
    Route::get('users/{department_id}',[UserController::class,'getAllDepartmentUser']);
Route::group(['middleware' => ['auth:sanctum', 'role:teacher|employee|admin|manager']], function () {
    Route::get('education_years',[SubjectGroupController::class,'educationYears']);
    Route::post('/update/login', [LoginController::class, 'updateLoginPassword']);
    Route::get('users',[UserController::class,'getAllUser']);
    Route::get('user/cameras',[UserController::class,'userCamera']);


    //generation
    Route::get('generation/week',[GenerationController::class,'getWeeks']);
    Route::get('own/schedules',[GenerationController::class,'getScheduleGroupBy']);
    Route::get('schedules/all',[GenerationController::class,'getAllScheduleGroupByDate']);
    Route::post('generation/schedule',[GenerationController::class,'generateSchedules']);
    //generation
});

Route::group(['prefix' => 'teacher', 'middleware' => ['auth:sanctum','role:teacher|admin']], function () {
    Route::get('/subjects',[SubjectController::class,'getAll']);
    Route::get('syllabus',[SyllabusController::class,'getAll']);
    Route::get('schedule/list',[ScheduleListController::class,'userSchedule']);
    Route::post('subject_groups',[SubjectGroupController::class,'store']);
    Route::get('own/subject_group', [SubjectGroupController::class,'getOwnSubjectGroup']);
});


