<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ImportantSettingsController;

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
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/', function () {
    return redirect(route('admin.dashboard'));
});

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('deeplink', [\App\Http\Controllers\DeeplinkController::class,'index'])->name('deeplink');
Route::get('qr-code/deeplink', [\App\Http\Controllers\DeeplinkController::class,'qrCode'])->name('qr-code.deeplink');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('login', [LoginController::class, 'login'])->name('auth.postlogin');

    Route::middleware(['auth'])->group(function () {
        Route::get('logout', [LoginController::class, 'logout'])->name('auth.logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //change password
        Route::get('change-password', [ProfileController::class, 'index'])->name('change.password');
        Route::post('submit-change-password', [ProfileController::class, 'submitChangePassword'])->name('submit.change.password');

        // user
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::post('users-list', [UserController::class, 'userList'])->name('users.list');
        Route::get('add-user', [UserController::class, 'userForm'])->name('users.add');
        Route::post('user/save', [UserController::class, 'userStore'])->name('users.save');
        Route::get('user/edit/{id}', [UserController::class, 'userEdit'])->name('users.edit');
        Route::post('user/edit/admin-access', [UserController::class, 'editAdminAccess'])->name('user.edit-admin-access');
        Route::post('user/edit/is-tutor', [UserController::class, 'editIsTutor'])->name('user.edit-is-tutor');
        Route::get('user/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
        Route::delete('user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::post('user/change-visit-status', [UserController::class, 'changeVisitStatus'])->name('user.change-visit-status');
        Route::post('user/edit/password', [UserController::class, 'editPassword'])->name('user.edit-password');

        //Banner
        Route::get('banner', [BannerController::class, 'index'])->name('banner');
        Route::get('add-banner', [BannerController::class, 'bannerForm'])->name('banner.add');

        //Important settings
        Route::get('important-settings', [ImportantSettingsController::class, 'index'])->name('important-settings');
        Route::post('important-settings/list', [ImportantSettingsController::class, 'settingsList'])->name('important-settings.list');
        Route::post('important-settings/update/on-off', [ImportantSettingsController::class, 'updateOnOff'])->name('important-settings.update.on-off');

        Route::get('proxy-image', [DashboardController::class,'proxyImage'])->name('proxy-image.item');

        //Coupon
        Route::get('coupon', [\App\Http\Controllers\Admin\CouponController::class,'index'])->name('coupon.index');
        Route::post('coupon/add', [\App\Http\Controllers\Admin\CouponController::class,'saveCoupon'])->name('coupon.store');
        Route::post('coupon/table', [\App\Http\Controllers\Admin\CouponController::class,'couponJsonData'])->name('coupon.table');
    });
});

Route::get('coupon/view', function (){
    return view('coupon');
});
Route::get('coupon/view/{id}', [\App\Http\Controllers\Admin\CouponController::class,'getImage']);
