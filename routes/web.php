<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SystemController;

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

Route::get('/',[SystemController::class,"home"])->name("home");
Route::get('/maut',[SystemController::class,"maut"])->name("maut");

Route::get('import-file', function () {
    return view('import');
});
Route::post("import-file",[SystemController::class,"import_data"])->name("import");

Route::get('/hasil', [SystemController::class,"hasil_import"])->name("hasil");
Route::get('olah-saw', [SystemController::class,"olah_saw"]);
Route::get('olah-maut', [SystemController::class,"olah_maut"]);

Route::get("test",[SystemController::class,"test"]);