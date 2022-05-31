<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FaceBookController;
use App\Http\Controllers\FireBaseController;
use App\Http\Controllers\DemoController;

use App\Http\Controllers\QucController;
use App\Events\Message;
use App\Http\Controllers\RealAccountsController;
use App\Http\Controllers\verifyController;
use App\Models\MtHulul;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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







// --------------------------------------------------------------------

// Routes to login or sign up using gmail account
// Route::get('auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle']);
// Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
// ----------------------------------------------

// Routes to login or sign up using facebook account
Route::prefix('/')->name('facebook.')->group(function () {
    Route::get('facebook/auth', [FaceBookController::class, 'loginUsingFacebook'])->name('login');
    Route::get('auth/facebook/callback', [FaceBookController::class, 'callbackFromFacebook'])->name('callback');
});
// -------------------------------------------------------
