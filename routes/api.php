<?php

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

Route::post(
    '/register',
    [App\Http\Controllers\Api\RegisterController::class, 'register']
)->name('register');

Route::match(
    ['get', 'post'],
    '/login',
    [App\Http\Controllers\Api\LoginController::class, 'login']
)->name('login');

Route::post(
    '/resend/email/token',
    [App\Http\Controllers\Api\RegisterController::class, 'resendPin']
)->name('resendPin');

Route::middleware('auth:sanctum')->group(function () {
    Route::post(
        'email/verify',
        [App\Http\Controllers\Api\RegisterController::class, 'verifyEmail']
    );
    Route::middleware('verify.api')->group(function () {
        Route::post(
            '/logout',
            [App\Http\Controllers\Api\LoginController::class, 'logout']
        );
    });

    Route::get(
        '/user',
        [App\Http\Controllers\Api\UserController::class, 'userProfile']
    );

    // Route::post(
    //     '/assignment/create',
    //     [App\Http\Controllers\Api\AssignmentController::class, 'create']
    // );

    // Route::get(
    //     '/assignmentc',
    //     [App\Http\Controllers\Api\AssignmentController::class, 'index']
    // );

    Route::group(['prefix' => '/assignment'], function () {

        Route::get('/', [App\Http\Controllers\Api\AssignmentController::class, 'index']);

        Route::post('/create', [App\Http\Controllers\Api\AssignmentController::class, 'create']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\AssignmentController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\AssignmentController::class, 'update']);

        Route::post('/add_report', [App\Http\Controllers\Api\AssignmentController::class, 'add_report']);

        Route::get('/delete', [App\Http\Controllers\Api\AssignmentController::class, 'destroy']);


    });

    Route::group(['prefix' => '/voucher'], function () {

        Route::get('/', [App\Http\Controllers\Api\VoucherController::class, 'index']);

        Route::post('/create', [App\Http\Controllers\Api\VoucherController::class, 'create']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\VoucherController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\VoucherController::class, 'update']);

        Route::post('/add_report', [App\Http\Controllers\Api\VoucherController::class, 'add_report']);

        Route::get('/delete', [App\Http\Controllers\Api\VoucherController::class, 'destroy']);


    });

    Route::group(['prefix' => '/overlay'], function () {

        Route::get('/', [App\Http\Controllers\Api\OverlayController::class, 'index']);

        Route::post('/create', [App\Http\Controllers\Api\OverlayController::class, 'create']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\OverlayController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\OverlayController::class, 'update']);

        Route::post('/add_report', [App\Http\Controllers\Api\OverlayController::class, 'add_report']);

        Route::get('/delete', [App\Http\Controllers\Api\OverlayController::class, 'destroy']);


    });

    Route::group(['prefix' => '/partner'], function () {

        Route::get('/', [App\Http\Controllers\Api\PartnerController::class, 'index']);

        Route::post('/create', [App\Http\Controllers\Api\PartnerController::class, 'create']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\PartnerController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\PartnerController::class, 'update']);

        Route::get('/delete', [App\Http\Controllers\Api\PartnerController::class, 'destroy']);


    });

    Route::group(['prefix' => '/user'], function () {

        Route::get('/list', [App\Http\Controllers\Api\UserController::class, 'index']);

        Route::get('/list/all', [App\Http\Controllers\Api\UserController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\UserController::class, 'create']);

        Route::post('/create_by_repairer', [App\Http\Controllers\Api\UserController::class, 'create_by_repairer']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\UserController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\UserController::class, 'update']);

        Route::post('/update_by_repairer', [App\Http\Controllers\Api\UserController::class, 'update_by_repairer']);

        Route::post('/reset_user_password', [App\Http\Controllers\Api\UserController::class, 'reset_user_password']);

        Route::post('/reset', [App\Http\Controllers\Api\UserController::class, 'reset']);

        Route::post('/enable', [App\Http\Controllers\Api\UserController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\UserController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\UserController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\UserController::class, 'destroy']);

    });

    Route::group(['prefix' => '/profile'], function () {

        Route::get('/', [App\Http\Controllers\Api\ProfilController::class, 'index']);

        Route::post('/create', [App\Http\Controllers\Api\ProfilController::class, 'create']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\ProfilController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\ProfilController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\ProfilController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\ProfilController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\ProfilController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\ProfilController::class, 'destroy']);

    });

    Route::group(['prefix' => '/repairer'], function () {

        Route::get('/', [App\Http\Controllers\Api\RepairerController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\RepairerController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\RepairerController::class, 'create']);

        Route::get('/{id}', [App\Http\Controllers\Api\RepairerController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\RepairerController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\RepairerController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\RepairerController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\RepairerController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\RepairerController::class, 'destroy']);

    });

    Route::group(['prefix' => '/client'], function () {

        Route::get('/', [App\Http\Controllers\Api\ClientController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\ClientController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\ClientController::class, 'create']);

        Route::get('/{id}', [App\Http\Controllers\Api\ClientController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\ClientController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\ClientController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\ClientController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\ClientController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\ClientController::class, 'destroy']);

    });

    Route::group(['prefix' => '/vehicle'], function () {

        Route::get('/', [App\Http\Controllers\Api\VehicleController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\VehicleController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\VehicleController::class, 'create']);

        Route::get('/{id}', [App\Http\Controllers\Api\VehicleController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\VehicleController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\VehicleController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\VehicleController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\VehicleController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\VehicleController::class, 'destroy']);

    });

    Route::group(['prefix' => '/designation'], function () {

        Route::get('/', [App\Http\Controllers\Api\DesignationController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\DesignationController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\DesignationController::class, 'create']);

        Route::post('/create-all', [App\Http\Controllers\Api\DesignationController::class, 'createAll']);

        Route::get('/{id}', [App\Http\Controllers\Api\DesignationController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\DesignationController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\DesignationController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\DesignationController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\DesignationController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\DesignationController::class, 'destroy']);

    });

    Route::group(['prefix' => '/shock-point'], function () {

        Route::get('/', [App\Http\Controllers\Api\ShockPointController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\ShockPointController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\ShockPointController::class, 'create']);

        Route::post('/create-all', [App\Http\Controllers\Api\ShockPointController::class, 'createAll']);

        Route::get('/{id}', [App\Http\Controllers\Api\ShockPointController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\ShockPointController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\ShockPointController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\ShockPointController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\ShockPointController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\ShockPointController::class, 'destroy']);

    });

    Route::group(['prefix' => '/repair-work'], function () {

        Route::get('/', [App\Http\Controllers\Api\RepairWorkController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\RepairWorkController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\RepairWorkController::class, 'create']);

        Route::get('/{uuid}', [App\Http\Controllers\Api\RepairWorkController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\RepairWorkController::class, 'update']);

        Route::get('/delete', [App\Http\Controllers\Api\RepairWorkController::class, 'destroy']);

    });

    Route::group(['prefix' => '/repair'], function () {

        Route::get('/', [App\Http\Controllers\Api\RepairController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\RepairController::class, 'all']);

        Route::get('/all-remark', [App\Http\Controllers\Api\RepairController::class, 'all_remark']);

        Route::post('/create', [App\Http\Controllers\Api\RepairController::class, 'create']);

        Route::post('/replay', [App\Http\Controllers\Api\RepairController::class, 'replay']);

        Route::get('/{id}', [App\Http\Controllers\Api\RepairController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\RepairController::class, 'update']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\RepairController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\RepairController::class, 'destroy']);

        Route::post('/add_before_photos', [App\Http\Controllers\Api\RepairController::class, 'add_before_photos']);

        Route::post('/add_during_photos', [App\Http\Controllers\Api\RepairController::class, 'add_during_photos']);

        Route::post('/add_after_photos', [App\Http\Controllers\Api\RepairController::class, 'add_after_photos']);

    });

    Route::group(['prefix' => '/brand'], function () {

        Route::get('/', [App\Http\Controllers\Api\BrandController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\BrandController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\BrandController::class, 'create']);

        Route::post('/create-all', [App\Http\Controllers\Api\BrandController::class, 'createAll']);

        Route::get('/{id}', [App\Http\Controllers\Api\BrandController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\BrandController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\BrandController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\BrandController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\BrandController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\BrandController::class, 'destroy']);

    });

    Route::group(['prefix' => '/vehicle-model'], function () {

        Route::get('/', [App\Http\Controllers\Api\VehicleModelController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\VehicleModelController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\VehicleModelController::class, 'create']);

        Route::post('/create-all', [App\Http\Controllers\Api\VehicleModelController::class, 'createAll']);

        Route::get('/{id}', [App\Http\Controllers\Api\VehicleModelController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\VehicleModelController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\VehicleModelController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\VehicleModelController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\VehicleModelController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\VehicleModelController::class, 'destroy']);

    });

    Route::group(['prefix' => '/color'], function () {

        Route::get('/', [App\Http\Controllers\Api\ColorController::class, 'index']);

        Route::get('/all', [App\Http\Controllers\Api\ColorController::class, 'all']);

        Route::post('/create', [App\Http\Controllers\Api\ColorController::class, 'create']);

        Route::post('/create-all', [App\Http\Controllers\Api\ColorController::class, 'createAll']);

        Route::get('/{id}', [App\Http\Controllers\Api\ColorController::class, 'show']);

        Route::post('/update', [App\Http\Controllers\Api\ColorController::class, 'update']);

        Route::post('/enable', [App\Http\Controllers\Api\ColorController::class, 'enable']);

        Route::post('/disable', [App\Http\Controllers\Api\ColorController::class, 'disable']);

        Route::get('/search/{information}', [App\Http\Controllers\Api\ColorController::class, 'search']);

        Route::get('/delete', [App\Http\Controllers\Api\ColorController::class, 'destroy']);

    });

});

/////*********************** */

// Route::post(
//     '/forgot-password',
//     [App\Http\Controllers\Api\ForgotPasswordController::class, 'forgotPassword']
// );
// Route::post(
//     '/verify/pin',
//     [App\Http\Controllers\Api\ForgotPasswordController::class, 'verifyPin']
// );
// Route::post(
//     '/reset-password',
//     [App\Http\Controllers\Api\ResetPasswordController::class, 'resetPassword']
// );

Route::group(['prefix' => '/user'], function () {
    Route::post('/reset_password', [App\Http\Controllers\Api\UserController::class, 'reset_password']);
});

Route::get('/test', [App\Http\Controllers\Api\RepairController::class, 'index']);

