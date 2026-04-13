<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'DashboardController@index')->name('home');

    $router->resource('affiliates', AffiliateController::class);
    $router->resource('applications', ApplicationController::class);
    $router->resource('attributions', AttributionController::class)->only(['index', 'show']);
    $router->resource('commissions', CommissionController::class);
    $router->resource('coupons', CouponController::class)->except(['show']);
    $router->resource('payouts', PayoutController::class)->except(['show']);

    $router->get('settings', 'SettingController@index');
    $router->get('logs/webhooks', 'WebhookLogController@index');
    $router->get('logs/tracking', 'TrackingLogController@index');
});
