<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return 'api....';
});

Route::prefix('request')->group(function () {
    Route::get('/', [ItemsController::class, 'getRequestItems']);
    Route::get('/items', [ItemsController::class, 'getItems']);
    Route::post('/add', [ItemsController::class, 'addRequestItem']);
    Route::get('/departement', [ItemsController::class, 'getUserByNik']);
});
