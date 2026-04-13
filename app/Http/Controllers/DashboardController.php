<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateApplication;
use App\Models\AffiliateAttribution;
use App\Models\AffiliateClick;
use App\Models\AffiliateCommission;
use App\Models\Shop;
use App\Models\WebhookLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $shopId = $request->integer('shop_id');
        $today = Carbon::today();

        $affiliateBase = Affiliate::query();
        $applicationBase = AffiliateApplication::query();
        $clickBase = AffiliateClick::query();
        $attributionBase = AffiliateAttribution::query();
        $commissionBase = AffiliateCommission::query();

        if ($shopId) {
            $affiliateBase->where('shop_id', $shopId);
            $applicationBase->where('shop_id', $shopId);
            $clickBase->where('shop_id', $shopId);
            $attributionBase->where('shop_id', $shopId);
            $commissionBase->where('shop_id', $shopId);
        }

        return response()->json([
            'total_affiliates' => (clone $affiliateBase)->count(),
            'pending_applications' => (clone $applicationBase)->where('status', 'pending')->count(),
            'today_clicks' => (clone $clickBase)->whereDate('created_at', $today)->count(),
            'today_attributed_orders' => (clone $attributionBase)->whereDate('created_at', $today)->count(),
            'pending_commission_amount' => (float) (clone $commissionBase)->where('status', 'pending')->sum('commission_amount'),
            'paid_commission_amount' => (float) (clone $commissionBase)->where('status', 'paid')->sum('commission_amount'),
        ]);
    }

    public function trends(Request $request)
    {
        $shopId = $request->integer('shop_id');
        $days = min(max($request->integer('days', 7), 7), 30);
        $startDate = Carbon::today()->subDays($days - 1);

        $clicks = AffiliateClick::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as value')
            ->when($shopId, fn ($q) => $q->where('shop_id', $shopId))
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('value', 'date');

        $orders = AffiliateAttribution::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as value')
            ->when($shopId, fn ($q) => $q->where('shop_id', $shopId))
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('value', 'date');

        $commissions = AffiliateCommission::query()
            ->selectRaw('DATE(created_at) as date, SUM(commission_amount) as value')
            ->when($shopId, fn ($q) => $q->where('shop_id', $shopId))
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('value', 'date');

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $series[] = [
                'date' => $date,
                'clicks' => (int) ($clicks[$date] ?? 0),
                'attributed_orders' => (int) ($orders[$date] ?? 0),
                'commission_amount' => (float) ($commissions[$date] ?? 0),
            ];
        }

        return response()->json(['days' => $days, 'series' => $series]);
    }

    public function todos(Request $request)
    {
        $shopId = $request->integer('shop_id');

        $todos = [
            'pending_applications' => AffiliateApplication::query()
                ->when($shopId, fn ($q) => $q->where('shop_id', $shopId))
                ->where('status', 'pending')
                ->latest()
                ->limit(10)
                ->get(),
            'pending_commissions' => AffiliateCommission::query()
                ->when($shopId, fn ($q) => $q->where('shop_id', $shopId))
                ->where('status', 'pending')
                ->latest()
                ->limit(10)
                ->get(),
            'failed_webhooks' => WebhookLog::query()
                ->when($shopId, fn ($q) => $q->where('shop_id', $shopId))
                ->where('process_status', 'failed')
                ->latest()
                ->limit(10)
                ->get(),
            'shop_health' => Shop::query()
                ->when($shopId, fn ($q) => $q->whereKey($shopId))
                ->select(['id', 'shop_domain', 'is_active', 'app_embedded_enabled', 'app_proxy_enabled'])
                ->get(),
        ];

        return response()->json($todos);
    }
}
