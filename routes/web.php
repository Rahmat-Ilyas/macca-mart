<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AdminController::class, 'home']);
Route::get('admin/', [AdminController::class, 'home']);
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [LoginController::class, 'login_page'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    Route::get('/', [AdminController::class, 'home'])->name('admin.home');

    Route::post('/config', [AdminController::class, 'config']);
    Route::post('/config/datatable', [AdminController::class, 'datatable']);
    Route::post('/store/{target}', [AdminController::class, 'store']);
    Route::post('/update/{target}', [AdminController::class, 'update']);
    Route::get('/delete/{target}/{id}', [AdminController::class, 'delete']);

    Route::get('/{page}', [AdminController::class, 'page']);
    Route::get('/{dir}/{page}', [AdminController::class, 'pagedir']);
    Route::get('/{dir}/{page}/{id}', [AdminController::class, 'pagedir_id']);
});
