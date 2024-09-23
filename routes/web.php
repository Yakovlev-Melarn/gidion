<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController as Login;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\SearchController;

Route::get('/login', [Login::class, 'index'])->name('login');
Route::post('/login/auth', [Login::class, 'auth']);
Route::get('/login/logout', [Login::class, 'logout']);
Route::post('/', [IndexController::class, "charts"])->middleware('auth');
Route::get('/', [IndexController::class, "index"])->middleware('auth');
Route::get('/{date}', [IndexController::class, "index"])->middleware('auth');
Route::get('/settings/addSeller', [SettingsController::class, 'addSeller'])->middleware('auth');
Route::post('/settings/addSeller', [SettingsController::class, 'addSeller'])->middleware('auth');
Route::get('/settings/sellers', [SettingsController::class, 'sellers'])->middleware('auth');
Route::post('/settings/updateSeller', [SettingsController::class, 'updateSeller'])->middleware('auth');
Route::get('/settings/changeSeller/{id}', [SettingsController::class, 'changeSeller'])->middleware('auth');
Route::get('/settings/process', [SettingsController::class, 'process'])->middleware('auth');
Route::post('/settings/process/run', [SettingsController::class, 'runProcess'])->middleware('auth');
Route::get('/shop/stocks', [ShopController::class, 'stocks'])->middleware('auth');
Route::get('/shop/stocks/{wh}', [ShopController::class, 'stock'])->middleware('auth');
Route::get('/shop/stocks/{wh}/{withoutSPrice}', [ShopController::class, 'stock'])->middleware('auth');
Route::get('/shop/saled/{date?}', [ShopController::class, 'saled'])->middleware('auth');
Route::get('/shop/ordered/{date?}', [ShopController::class, 'ordered'])->middleware('auth');
Route::get('/shop/orders', [ShopController::class, 'orders'])->middleware('auth');
Route::get('/shop/orders/{shipmentId}', [ShopController::class, 'orders'])->middleware('auth');
Route::post('/shop/printAll', [ShopController::class, 'printAll'])->middleware('auth');
Route::post('/shop/print', [ShopController::class, 'printOrderBarcode'])->middleware('auth');
Route::post('/shop/updateWhl', [ShopController::class, 'updateWhl'])->middleware('auth');
Route::post('/shop/orderComplete', [ShopController::class, 'orderComplete'])->middleware('auth');
Route::get('/cards/comission', [CardController::class, 'comission'])->middleware('auth');
Route::get('/cards/getRules', [CardController::class, 'getRules'])->middleware('auth');
Route::post('/cards/uploadCard', [CardController::class, 'uploadCard'])->middleware('auth');
Route::post('/cards/saveRule', [CardController::class, 'saveRule'])->middleware('auth');
Route::get('/cards/getCharc/{subjectId}', [CardController::class, 'getCharc'])->middleware('auth');
Route::get('/cards/getObjectsAll', [CardController::class, 'getObjectsAll'])->middleware('auth');
Route::get('/cards/delete', [CardController::class, 'deleteCards'])->middleware('auth');
Route::post('/cards/delete', [CardController::class, 'deleteCards'])->middleware('auth');
Route::get('/cards/list/{page}', [CardController::class, 'getList'])->middleware('auth');
Route::post('/cards/changeFilter', [CardController::class, 'changeFilter'])->middleware('auth');
Route::get('/cards/copy', [CardController::class, 'copy'])->middleware('auth');
Route::post('/cards/copy', [CardController::class, 'copy'])->middleware('auth');
Route::get('/cards/catalog', [CardController::class, 'catalog'])->middleware('auth');
Route::post('/cards/catalog', [CardController::class, 'catalog'])->middleware('auth');
Route::get('/cards/createcard/{cardId}', [CardController::class, 'createcard'])->middleware('auth');
Route::post('/card/trash/', [CardController::class, 'trash'])->middleware('auth');
Route::get('/card/{id}', [CardController::class, 'getCardInfo'])->middleware('auth');
Route::post('/card/print', [CardController::class, 'printBarcode'])->middleware('auth');
Route::post('/card/{id}', [CardController::class, 'updateCardInfo'])->middleware('auth');
Route::get('/card/getSellStockPrice/{id}', [CardController::class, 'getSellStockPrice'])->middleware('auth');
Route::get('/settings/competitors', [SettingsController::class, 'competitors'])->middleware('auth');
Route::get('/settings/addCompetitor', [SettingsController::class, 'addCompetitor'])->middleware('auth');
Route::post('/settings/addCompetitor', [SettingsController::class, 'addCompetitor'])->middleware('auth');
Route::post('/settings/deleteCompetitor', [SettingsController::class, 'deleteCompetitor'])->middleware('auth');
Route::get('/settings/suppliers', [SettingsController::class, 'suppliers'])->middleware('auth');
Route::get('/settings/addSupplier', [SettingsController::class, 'addSupplier'])->middleware('auth');
Route::post('/settings/addSupplier', [SettingsController::class, 'addSupplier'])->middleware('auth');
Route::post('/settings/deleteSupplier', [SettingsController::class, 'deleteSupplier'])->middleware('auth');
Route::post('/search', [SearchController::class, 'search'])->middleware('auth');
