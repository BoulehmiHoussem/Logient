<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinksController;
use App\Http\Controllers\LangController;

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
    return redirect()->route('link.index');
});


Route::prefix('/dashboard')->group(function(){
    
    //Authentification routes
    Auth::routes();

    //change language
    Route::get('/lang/change', [LangController::class, 'change'])->name('lang.change');
    
    
    //Links Management 
    Route::middleware('auth')->group(function () { 
    
        // Links EndPoints
        Route::resource('/link' , LinksController::class)->only([
            'index', 'store', 'destroy', 'create'
        ]);
    
    });
});


//Shortcut route
Route::get('/{shortcut}', [LinksController::class, 'show'])->middleware('log')->name('link.shortcut');

