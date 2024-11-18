<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\SellersController;

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

Route::get('/getSellerList', [SellersController::class, 'getSellersList'])->middleware("apiauth");
Route::get('/getSelectedSeller', [SellersController::class, 'getSelectedSeller'])->middleware("apiauth");
Route::post('/setSelectedSeller', [SellersController::class, 'setSelectedSeller'])->middleware("apiauth");
