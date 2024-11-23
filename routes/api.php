<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\QrCodeController;

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

Route::post('register', [UserAuthController::class, 'register']); //normal user signup
Route::post('login', [UserAuthController::class, 'login']); //for admin,normal user, tutor

Route::post('google_signup', [UserAuthController::class, 'google_signup']);
Route::post('google_login', [UserAuthController::class, 'google_login']);

Route::post('kakao_signup', [UserAuthController::class, 'kakao_signup']);
Route::post('kakao_login', [UserAuthController::class, 'kakao_login']);

Route::post('apple_signup', [UserAuthController::class, 'apple_signup']);
Route::post('apple_login', [UserAuthController::class, 'apple_login']);

Route::post('check-account-exist', [UserAuthController::class,'checkAccountExist']);

Route::post('forgot/password', [UserAuthController::class, 'forgotPassword'])->name('auth.forgot.password'); // for find pwd
Route::post('update/password', [UserAuthController::class, 'changePassword'])->name('auth.update.password'); // for find pwd
Route::middleware('auth:api')->group( function () {
    Route::post('logout', [UserController::class,'logout']);

    Route::get('user-detail', [UserController::class,'userDetail']);
    Route::get('user-list', [UserController::class,'userList']);
    Route::post('search-user', [UserController::class,'searchUser']);

    Route::post('add_daily_diary', [UserController::class,'addDiary']); //for normal user

    Route::post('generate-qr-code', [QrCodeController::class,'generateQRCode']);
    Route::post('get-qr-code', [QrCodeController::class,'getQRCode']);
    Route::post('scan-qr-code', [QrCodeController::class,'scanQRCode']);

    Route::post('member-list', [UserController::class, 'MemberList']);
    Route::post('change-visit-status', [UserController::class, 'changeVisitStatus']);

    Route::post('home', [UserController::class, 'home']);
    Route::get('user-profile', [UserController::class, 'profileInfo']);
    Route::post('remove-user-profile',[UserController::class, 'deleteUser']);
});

Route::fallback(function () {
    return response()->json(['error' => true,'status' => 404,'message' => 'Api Not Found!'], 404);
});
