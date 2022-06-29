<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogicController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("kriteria",[LogicController::class,"get_kriteria"])->name("kriteria.data");
Route::post("kriteria",[LogicController::class,"store_kriteria"])->name("kriteria.store");
Route::get("wilayah",[LogicController::class,"get_wilayah"])->name("wilayah");
Route::post("data-alternatif",[LogicController::class,"get_data_alternatif"])->name("data.alternatif");
