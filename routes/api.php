<?php

use App\Http\Controllers\API\apiAffiliatesController;
use App\Http\Controllers\API\apiDepositWithdrawController;
use App\Http\Controllers\API\apiVerifyController;
use App\Http\Controllers\API\apiEmailVerificationController;
use App\Http\Controllers\API\apiRealAccountsController;
use App\Http\Controllers\API\apiStatusInfoController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\PhoneForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//API route for register new user
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
//API route for Get current country
Route::get('/Get-current-country', [App\Http\Controllers\API\AuthController::class, 'getCurrentCountry']);
// API route for logout user
Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout'])->middleware('auth:sanctum');
//////////////////////////////////////////////////////////////////////
Route::post('password/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('passwords.sent');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('passwords.reset');
///////////////////////////////////////////////////////////////////////
Route::get('/send-phone-code', [PhoneForgotPasswordController::class, 'sendResetCode'])->name('sendResetCode');
Route::post('/check-phone-code', [PhoneForgotPasswordController::class, 'CheckCode'])->name('CheckCode');
Route::post('/phone/reset/password', [PhoneForgotPasswordController::class, 'reset'])->name('reset');
//////////////////////////////////////////////////////////////////////
Route::get('/verifyPhone', [apiVerifyController::class, 'verifyPhone'])->name('Api.verifyPhone');
Route::post('/phoneVerifyCode', [apiVerifyController::class, 'verify'])->name('api.phoneVerifyCode');
/////////////////////////////////////////////////////////////////////
Route::get('/send-Verification-Email', [apiEmailVerificationController::class, 'sendVerificationEmail']);
Route::get('verify-email/{id}/{hash}', [apiEmailVerificationController::class, 'verify'])->name('verification.verify');
//////////////////////////////////////////////////////////////////////
Route::get('/getUserDemoAccounts', [\App\Http\Controllers\API\apiUserAccountsController::class, 'getDemoAccounts']);
Route::get('/getUserRealAccounts', [\App\Http\Controllers\API\apiUserAccountsController::class, 'showTrading']);
///////////////////////////////////////////////////////////////////////
Route::get('/getAllUserAffiliates', [apiAffiliatesController::class, 'getAllAffiliates']);
Route::get('/getUserReferralLink', [apiAffiliatesController::class, 'getReferralLink']);
//////////////////////////////////////////////////////////////////////
Route::get('/getStateInfo', [apiStatusInfoController::class, 'getStateInfo']);
Route::get('/getStatePage/{login}', [apiStatusInfoController::class, 'show'])->name('state.show');
Route::get('/demos/filter', [apiStatusInfoController::class, 'filter'])->name('state.filter');

////////////////////////////////////////////////////////////////////////

Route::post('/deposit&withdraw', [apiDepositWithdrawController::class, 'store'])->name('deposit&withdraw.store');

///////////////////////////////////////////////////////////////////////
Route::post('/store-user-basic-information', [\App\Http\Controllers\API\apiBasicInformationController::class, 'store'])->name('aipBasicInfoStore');
Route::put('/update-user-basic-information/{userId}', [\App\Http\Controllers\API\apiBasicInformationController::class, 'update'])->name('aipBasicInfoUpdate');
Route::get('/get-user-basic-information', [\App\Http\Controllers\API\apiBasicInformationController::class, 'getUserBasicInfo'])->name('aipGetUserBasicInfo');
Route::post('/change-user-password', [\App\Http\Controllers\API\apiBasicInformationController::class, 'changePassword'])->name('apiChangePassword');
//////////////////////////////////////////////////////////////////////
Route::post('/store-Documents', [App\Http\Controllers\API\apiDocumentController::class, 'store'])->name('storeDocuments');
Route::put('/user-update-Documents/{id}', [App\Http\Controllers\API\apiDocumentController::class, 'UserUpdate'])->name('UserUpdateDocuments');
Route::get('/get-user-Documents', [App\Http\Controllers\API\apiDocumentController::class, 'getUserDocuments'])->name('getUserDocuments');
///////////////////////////////////////////////////////////////////////
Route::get('/get-user-Quc', [App\Http\Controllers\API\apiQucController::class, 'getUserQuc'])->name('getUserQuc');
Route::post('/store-user-Quc', [App\Http\Controllers\API\apiQucController::class, 'storeQuc'])->name('storeUserQuc');

Route::post('/create-demo', [App\Http\Controllers\API\apiDemoController::class, 'store'])->name('createDemo');
Route::delete('/delete-account/{login}', [App\Http\Controllers\API\apiDemoController::class, 'destroy'])->name('deleteAccount');
Route::put('/demos/leverage/updateApi/{login}', [App\Http\Controllers\API\apiDemoController::class, 'leverageUpdate'])->name('leverageUpdateApi');
Route::put('/demos/balance/updateApi/{login}', [App\Http\Controllers\API\apiDemoController::class, 'changeBalance'])->name('changeBalanceApi');


Route::post('/Api-store-real-account', [apiRealAccountsController::class, 'store'])->name('realAccountApi.store');
//////////////////////////////////////////////////

Route::get('auth/google', [App\Http\Controllers\API\GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
