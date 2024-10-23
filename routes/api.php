<?php

use App\Http\Controllers\Clients\ClientsController;
use App\Http\Controllers\Clients\ClientWorksController;
use App\Http\Controllers\Subscribers\SubscribersController;
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

//clients routes
Route::resource('/clients', ClientsController::class)->except([
    'edit', 'create'
]);
Route::resource('/client_works', ClientWorksController::class)->except([
    'edit', 'create'
]);

//subscribers routes
Route::resource('/subscribers', SubscribersController::class)->except([
    'edit', 'create'
]);
Route::resource('/subscribers_works', ClientWorksController::class)->except([
    'edit', 'create'
]);
