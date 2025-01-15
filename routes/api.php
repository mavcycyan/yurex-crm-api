<?php

use App\Http\Controllers\Advocates\AdvocateCasesController;
use App\Http\Controllers\Advocates\AdvocateCategoriesController;
use App\Http\Controllers\Advocates\AdvocatesController;
use App\Http\Controllers\Advocates\AdvocateWorksController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Clients\ClientsController;
use App\Http\Controllers\Clients\ClientWorksController;
use App\Http\Controllers\Subscribers\SubscribersController;
use App\Http\Controllers\Subscribers\SubscriberWorksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//clients routes
Route::middleware(['auth:sanctum', 'role:root,director,lawyer'])->group(function () {
    Route::resource('/clients', ClientsController::class)->except([
        'edit', 'create'
    ]);
    Route::resource('/client_works', ClientWorksController::class)->except([
        'edit', 'create'
    ]);
});

//subscribers routes
Route::middleware(['auth:sanctum', 'role:root,director,lawyer'])->group(function () {
    Route::resource('/subscribers', SubscribersController::class)->except([
        'edit', 'create'
    ]);
    Route::resource('/subscriber_works', SubscriberWorksController::class)->except([
        'edit', 'create'
    ]);
});

//advocates routes
Route::middleware(['auth:sanctum', 'role:root,director,advocate'])->group(function () {
    Route::resource('/advocates', AdvocatesController::class)->except([
        'edit', 'create', 'store', 'destroy'
    ]);
    Route::resource('/advocates_categories', AdvocateCategoriesController::class)->except([
        'edit', 'create'
    ]);
    Route::get('/advocates_categories_byslug', [AdvocateCategoriesController::class, 'byslug']);
    Route::resource('/advocates_cases', AdvocateCasesController::class)->except([
        'edit', 'create'
    ]);
    Route::resource('/advocates_works', AdvocateWorksController::class)->except([
        'edit', 'create'
    ]);
});
