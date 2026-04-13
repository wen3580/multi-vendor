<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateApplication;
use App\Models\AffiliateAttribution;
use App\Models\AffiliateClick;
use App\Models\AffiliateCommission;
use App\Models\WebhookLog;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Table;

class DashboardController extends Controller
{
    public function index(Content $content): Content
    {
        $today = now()->startOfDay();

        $metrics = [
            'affiliates' => Affiliate::count(),
            'pendingApplications' => AffiliateApplication::where('status', 'pending')->count(),
            'todayClicks' => AffiliateClick::where('created_at', '>=', $today)->count(),
            'todayOrders' => AffiliateAttribution::where('created_at', '>=', $today)->count(),
            'pendingCommissions' => (float) AffiliateCommission::where('status', 'pending')->sum('commission_amount'),
            'paidCommissions' => (float) AffiliateCommission::where('status', 'paid')->sum('commission_amount'),
        ];

        $webhookFailed = WebhookLog::where('process_status', 'failed')->where('created_at', '>=', now()->subDay())->count();

        return $content
            ->title('Affiliate Dashboard')
            ->description('Shopify 分销业务总览')
            ->row(function ($row) use ($metrics) {
                $row->column(4, new InfoBox('推广员总数', 'users', 'aqua', admin_url('affiliates'), (string) $metrics['affiliates']));
                $row->column(4, new InfoBox('待审核申请', 'file-text', 'yellow', admin_url('applications'), (string) $metrics['pendingApplications']));
                $row->column(4, new InfoBox('今日点击', 'mouse-pointer', 'green', admin_url('logs/tracking'), (string) $metrics['todayClicks']));
            })
            ->row(function ($row) use ($metrics) {
                $row->column(4, new InfoBox('今日归因订单', 'shopping-cart', 'purple', admin_url('attributions'), (string) $metrics['todayOrders']));
                $row->column(4, new InfoBox('待审核佣金', 'money', 'orange', admin_url('commissions'), number_format($metrics['pendingCommissions'], 2)));
                $row->column(4, new InfoBox('已打款佣金', 'bank', 'teal', admin_url('payouts'), number_format($metrics['paidCommissions'], 2)));
            })
            ->row(function ($row) use ($webhookFailed) {
                $row->column(12, new Table(['检查项', '状态'], [
                    ['Shopify 授权状态', '待接入实时检查（V1）'],
                    ['Webhook 最近 24h 失败数', $webhookFailed],
                    ['Tracking 状态', '已启用点击记录'],
                ]));
            });
    }
}
