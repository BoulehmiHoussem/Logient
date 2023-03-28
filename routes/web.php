<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinksController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/{shortcut}', [LinksController::class, 'show'])->name('link.shortcut');
Route::middleware('auth')->group(function () { 

    // Links EndPoints
    Route::resource('/link' , LinksController::class)->only([
        'index', 'store', 'destroy', 'create'
    ]);

});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
