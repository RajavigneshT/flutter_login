<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRUDController;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BusRouteController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\CreateChildController;

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
//Protected Route
Route::group(['middleware' => ['web', 'auth:sanctum']], function () {
    Route::post('/createpayment', [PaymentController::class, 'create_payment'])->name('createpayment');
    Route::post('/createbusroute', [BusRouteController::class, 'createbusroute'])->name('createbusroute');
});

//Public Route
Route::post('/createchild', [CreateChildController::class, 'createChild'])->name('createchild');


Route::get('/usershow', [UserReportController::class, 'usershow']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/logout', [AuthApiController::class, 'logout']);
Route::post('/forgot', [AuthApiController::class, 'forgot'])->name('forgot');
Route::post('/resetpassword', [AuthApiController::class, 'resetpassword'])->name('resetpassword');
Route::get('/refresh', [AuthApiController::class, 'refresh']);
Route::put('/updatechild/{id}', [CreateChildController::class, 'updatechild']);
Route::delete('/deletechild/{id}', [CreateChildController::class, 'deletechild'])->name('deletechild');


