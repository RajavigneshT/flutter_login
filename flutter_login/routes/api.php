<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\CRUDController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']); 
Route::post('/logout',[AuthApiController::class,'logout']);
Route::post('/forgotpassword',[AuthApiController::class,'forgotPassword']);
Route::get('/refresh',[AuthApiController::class,'refresh']);

Route::get('api/usershow',[CRUDController::class,'show']);