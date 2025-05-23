<?php

use App\Http\Controllers\Advocates\AdvocateCasesController;
use App\Http\Controllers\Advocates\AdvocateCategoriesController;
use App\Http\Controllers\Advocates\AdvocatesController;
use App\Http\Controllers\Advocates\AdvocateWorksController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Calculations\SubscrCalculations;
use App\Http\Controllers\Clients\ClientsController;
use App\Http\Controllers\Clients\ClientWorksController;
use App\Http\Controllers\Files\Advocate\FilesAdvocateWorks;
use App\Http\Controllers\Files\Clients\FilesClientWorks;
use App\Http\Controllers\Files\Subscriber\FilesSubscriberWorks;
use App\Http\Controllers\Finances\Advocates\FinAdvocateCasesPrices;
use App\Http\Controllers\Finances\Clients\FinClientWorksPrices;
use App\Http\Controllers\Finances\Subscribers\FinSubscribersPrices;
use App\Http\Controllers\Finances\Subscribers\FinSubscrWorksPrices;
use App\Http\Controllers\Subscribers\SubscribersController;
use App\Http\Controllers\Subscribers\SubscriberWorksController;
use App\Http\Controllers\Users\UsersController;
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

Route::middleware(['auth:sanctum', 'role:root,director'])->post('/register', [AuthController::class, 'register']);
Route::middleware(['auth:sanctum', 'role:root,director'])->post('/password_reset/{id}', [AuthController::class, 'password_reset']);
Route::post('/login', [AuthController::class, 'login']);
//Route::get('/convert_client_case', [AuthController::class, 'convert_client_case']);

//users routes
Route::middleware(['auth:sanctum', 'role:root,director'])->group(function () {
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::put('/users/activity/{id}', [UsersController::class, 'activity']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
});

//clients routes
Route::middleware(['auth:sanctum', 'role:root,director,lawyer'])->group(function () {
    Route::resource('/clients', ClientsController::class)->except([
        'edit', 'create', 'destroy'
    ]);
    Route::put('/clients/activity/{id}', [ClientsController::class, 'activity']);
    Route::resource('/client_works', ClientWorksController::class)->except([
        'edit', 'create'
    ]);

    Route::get('/client_works_list', [ClientWorksController::class, 'get_by_client']);

    Route::post('/client_works/{id}/files', [FilesClientWorks::class, 'upload']);
    Route::delete('/client_works/{workid}/files/{id}', [FilesClientWorks::class, 'destroy']);
    Route::get('/client_works/{id}/files', [FilesClientWorks::class, 'getFiles']);

    Route::post('/lawyer_advocates_cases', [AdvocateCasesController::class, 'store']);
    Route::get('/lawyer_advocates_categories', [AdvocateCategoriesController::class, 'index']);
    Route::get('/lawyer_advocates_cases', [AdvocateCasesController::class, 'index']);
});
Route::middleware(['auth:sanctum', 'role:root,director'])->group(function () {
    Route::delete('/clients/{id}', [ClientsController::class, 'destroy']);
    Route::get('/archive/clients', [ClientsController::class, 'archive']);
//    Route::put('/client_works/activity/{id}', [ClientWorksController::class, 'activity']);
});

//subscribers routes
Route::middleware(['auth:sanctum', 'role:root,director,lawyer'])->group(function () {
    Route::resource('/subscribers', SubscribersController::class)->except([
        'edit', 'create', 'destroy'
    ]);
    Route::put('/subscribers/activity/{id}', [SubscribersController::class, 'activity']);
    Route::resource('/subscriber_works', SubscriberWorksController::class)->except([
        'edit', 'create'
    ]);
    Route::get('/lawyer_advocates', [AdvocatesController::class, 'index']);
    Route::get('/lawyer_workers', [UsersController::class, 'get_workers']);

    Route::post('/subscriber_works/{id}/files', [FilesSubscriberWorks::class, 'upload']);
    Route::delete('/subscriber_works/{workid}/files/{id}', [FilesSubscriberWorks::class, 'destroy']);
    Route::get('/subscriber_works/{id}/files', [FilesSubscriberWorks::class, 'getFiles']);
});
Route::middleware(['auth:sanctum', 'role:root,director'])->group(function () {
    Route::delete('/subscribers/{id}', [SubscribersController::class, 'destroy']);
    Route::get('/archive/subscribers', [SubscribersController::class, 'archive']);
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
        'edit', 'create', 'destroy'
    ]);
    Route::put('/advocates_cases/activity/{id}', [AdvocateCasesController::class, 'activity']);
    Route::resource('/advocates_works', AdvocateWorksController::class)->except([
        'edit', 'create'
    ]);
    Route::get('/get_client_case/{id}', [AdvocateCasesController::class, 'get_client_case']);
    Route::get('/advocate_clients', [ClientsController::class, 'index']);

    Route::post('/advocates_works/{id}/files', [FilesAdvocateWorks::class, 'upload']);
    Route::delete('/advocates_works/{workid}/files/{id}', [FilesAdvocateWorks::class, 'destroy']);
    Route::get('/advocates_works/{id}/files', [FilesAdvocateWorks::class, 'getFiles']);


    Route::resource('/advocates_clients', ClientsController::class)->except([
        'edit', 'create'
    ]);
});
Route::middleware(['auth:sanctum', 'role:root,director'])->group(function () {
    Route::delete('/advocates_cases/{id}', [AdvocateCasesController::class, 'destroy']);
    Route::get('/archive/advocates_cases', [AdvocateCasesController::class, 'archive']);
});

//finance routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/fin/clientworks/prices', FinClientWorksPrices::class)->except([
        'edit', 'create'
    ]);
    Route::put('/fin/clientworks/prices_bulk_update', [FinClientWorksPrices::class, 'bulk_update']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/fin/subscribers/prices', FinSubscribersPrices::class)->except([
        'edit', 'create'
    ]);
    Route::put('/fin/subscribers/prices_bulk_update', [FinSubscribersPrices::class, 'bulk_update']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/fin/subscriberworks/prices', FinSubscrWorksPrices::class)->except([
        'edit', 'create'
    ]);
    Route::put('/fin/subscriberworks/prices_bulk_update', [FinSubscrWorksPrices::class, 'bulk_update']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/fin/advocatecases/prices', FinAdvocateCasesPrices::class)->except([
        'edit', 'create'
    ]);
    Route::put('/fin/advocatecases/prices_bulk_update', [FinAdvocateCasesPrices::class, 'bulk_update']);
});

// subscribers calculations

Route::middleware(['auth:sanctum', 'role:root,director'])->group(function () {
    Route::resource('/calculations/subscribers', SubscrCalculations::class)->except([
        'edit', 'create', 'destroy'
    ]);
    Route::put('/subscribers_stop_reason/{id}', [SubscribersController::class, 'update_stop_reason']);
});
