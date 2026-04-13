<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::prefix('shopify')->group(function () {
    Route::get('/install', [OAuthController::class, 'install']);
    Route::get('/oauth/callback', [OAuthController::class, 'callback']);
});

Route::post('/webhooks/shopify', WebhookController::class);

Route::prefix('admin')->group(function () {
    Route::get('/affiliates', [AffiliateController::class, 'index']);
    Route::post('/affiliates', [AffiliateController::class, 'store']);
    Route::post('/affiliates/{affiliate}/approve', [AffiliateController::class, 'approve']);
    Route::post('/affiliates/{affiliate}/reject', [AffiliateController::class, 'reject']);

    Route::get('/commissions', [CommissionController::class, 'index']);
    Route::post('/commissions/{commission}/approve', [CommissionController::class, 'approve']);
    Route::post('/commissions/{commission}/void', [CommissionController::class, 'void']);

    Route::get('/coupons', [CouponController::class, 'index']);
    Route::post('/coupons/create', [CouponController::class, 'create']);
    Route::post('/coupons/{coupon}/disable', [CouponController::class, 'disable']);
});
