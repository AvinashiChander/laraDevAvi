<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\EmailController;
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

    //Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [EmailController::class, 'login'])->name('login');

    Route::middleware('auth:api')->group( function () {
        Route::post('invitation', [EmailController::class, 'invitation']);
        Route::post('update-profile/', [EmailController::class, 'updateProfile']);
        Route::get('profile/', [EmailController::class, 'profile']);
    });
    Route::get('registration/{code}', [EmailController::class, 'getCode'])->name('getCode');
    Route::post('registration/', [EmailController::class, 'registration'])->name('registration');
    Route::get('pincode/{code}', [EmailController::class, 'pincode'])->name('pincode');
    Route::post('enter-pin/', [EmailController::class, 'enterPin'])->name('enterPin');
    
    
    
    Route::fallback(function () {
        return abort(404);
        // return view('errors.404');  // incase you want to return view
    });