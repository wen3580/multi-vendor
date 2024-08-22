<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show(Request $request)
    {
        $request->validate(['shop_id' => ['required', 'integer']]);

        return Shop::query()->findOrFail($request->integer('shop_id'));
    }

    public function updateBasic(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'default_cookie_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $shop = Shop::query()->findOrFail($data['shop_id']);
        $shop->update(collect($data)->except('shop_id')->toArray());

        return response()->json(['ok' => true, 'shop' => $shop->fresh()]);
    }

    public function updateAttribution(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'default_cookie_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $shop = Shop::query()->findOrFail($data['shop_id']);
        $shop->update(['default_cookie_days' => $data['default_cookie_days'] ?? $shop->default_cookie_days]);

        return response()->json(['ok' => true, 'shop' => $shop->fresh()]);
    }

    public function updateCommission(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'default_commission_type' => ['nullable', 'in:percent,fixed'],
            'default_commission_value' => ['nullable', 'numeric', 'min:0'],
            'commission_approval_mode' => ['nullable', 'in:manual,auto'],
        ]);

        $shop = Shop::query()->findOrFail($data['shop_id']);
        $shop->update(collect($data)->except('shop_id')->toArray());

        return response()->json(['ok' => true, 'shop' => $shop->fresh()]);
    }

    public function updateCoupon(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'app_proxy_enabled' => ['nullable', 'boolean'],
        ]);

        $shop = Shop::query()->findOrFail($data['shop_id']);
        $shop->update(['app_proxy_enabled' => $data['app_proxy_enabled'] ?? $shop->app_proxy_enabled]);

        return response()->json(['ok' => true, 'shop' => $shop->fresh()]);
    }

    public function healthCheck(Request $request)
    {
        $request->validate(['shop_id' => ['required', 'integer']]);
        $shop = Shop::query()->findOrFail($request->integer('shop_id'));

        return response()->json([
            'shop_domain' => $shop->shop_domain,
            'authorization' => $shop->is_active ? 'active' : 'inactive',
            'webhook_status' => WebhookLog::query()->where('shop_id', $shop->id)->where('process_status', 'failed')->exists() ? 'warning' : 'ok',
            'app_proxy_enabled' => (bool) $shop->app_proxy_enabled,
            'app_embedded_enabled' => (bool) $shop->app_embedded_enabled,
            'recent_webhook_errors' => WebhookLog::query()->where('shop_id', $shop->id)->where('process_status', 'failed')->count(),
        ]);
    }
}
