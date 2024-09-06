<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::prefix('shopify')->group(function () {
    Route::get('/install', [OAuthController::class, 'install']);
    Route::get('/oauth/callback', [OAuthController::class, 'callback']);
});

Route::post('/webhooks/shopify', WebhookController::class);

Route::prefix('admin-api')->group(function () {
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/trends', [DashboardController::class, 'trends']);
    Route::get('/dashboard/todos', [DashboardController::class, 'todos']);

    Route::get('/affiliates', [AffiliateController::class, 'index']);
    Route::get('/affiliates/{affiliate}', [AffiliateController::class, 'show']);
    Route::post('/affiliates', [AffiliateController::class, 'store']);
    Route::put('/affiliates/{affiliate}', [AffiliateController::class, 'update']);
    Route::post('/affiliates/{affiliate}/approve', [AffiliateController::class, 'approve']);
    Route::post('/affiliates/{affiliate}/reject', [AffiliateController::class, 'reject']);
    Route::post('/affiliates/{affiliate}/block', [AffiliateController::class, 'block']);

    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve']);
    Route::post('/applications/{application}/reject', [ApplicationController::class, 'reject']);

    Route::get('/attributions', [AttributionController::class, 'index']);
    Route::get('/attributions/{attribution}', [AttributionController::class, 'show']);
    Route::post('/attributions/{attribution}/reassign', [AttributionController::class, 'reassign']);
    Route::post('/attributions/{attribution}/remove', [AttributionController::class, 'remove']);

    Route::get('/commissions', [CommissionController::class, 'index']);
    Route::get('/commissions/{commission}', [CommissionController::class, 'show']);
    Route::post('/commissions/{commission}/approve', [CommissionController::class, 'approve']);
    Route::post('/commissions/{commission}/void', [CommissionController::class, 'void']);
    Route::post('/commissions/{commission}/mark-paid', [CommissionController::class, 'markPaid']);
    Route::post('/commissions/{commission}/recalculate', [CommissionController::class, 'recalculate']);

    Route::get('/coupons', [CouponController::class, 'index']);
    Route::post('/coupons', [CouponController::class, 'store']);
    Route::post('/coupons/{coupon}/disable', [CouponController::class, 'disable']);
    Route::post('/coupons/{coupon}/enable', [CouponController::class, 'enable']);

    Route::get('/payouts', [PayoutController::class, 'index']);
    Route::post('/payouts', [PayoutController::class, 'store']);
    Route::get('/payouts/{payout}', [PayoutController::class, 'show']);
    Route::post('/payouts/{payout}/export', [PayoutController::class, 'export']);
    Route::post('/payouts/{payout}/mark-paid', [PayoutController::class, 'markPaid']);

    Route::get('/settings', [SettingController::class, 'show']);
    Route::put('/settings/basic', [SettingController::class, 'updateBasic']);
    Route::put('/settings/attribution', [SettingController::class, 'updateAttribution']);
    Route::put('/settings/commission', [SettingController::class, 'updateCommission']);
    Route::put('/settings/coupon', [SettingController::class, 'updateCoupon']);
    Route::get('/settings/health-check', [SettingController::class, 'healthCheck']);

    Route::get('/logs/webhooks', [LogController::class, 'webhooks']);
    Route::get('/logs/tracking', [LogController::class, 'tracking']);
    Route::get('/logs/errors', [LogController::class, 'errors']);
});
