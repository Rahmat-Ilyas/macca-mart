<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ApiTokenController;
use App\Http\Controllers\ApiController;

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

Route::post('login', [ApiTokenController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('get-data-sinkron', [ApiController::class, 'data_sinkron']);
    Route::get('get-last-date', [ApiController::class, 'tanggal_terakhir']);
    Route::get('get-count-data', [ApiController::class, 'get_count_data']);
    Route::post('sinkron', [ApiController::class, 'sinkron']);
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'URL Not Found'
    ], 404);
});
