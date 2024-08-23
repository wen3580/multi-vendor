<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;

class SettingController extends Controller
{
    public function index(Content $content): Content
    {
        $shop = Shop::query()->latest()->first();

        $rows = $shop ? [
            ['Shop Domain', $shop->shop_domain],
            ['Cookie Days', (string) $shop->default_cookie_days],
            ['Commission Type', $shop->default_commission_type],
            ['Commission Value', (string) $shop->default_commission_value],
            ['Commission Approval Mode', $shop->commission_approval_mode],
            ['App Embed Enabled', $shop->app_embedded_enabled ? 'Yes' : 'No'],
            ['App Proxy Enabled', $shop->app_proxy_enabled ? 'Yes' : 'No'],
            ['Scopes', $shop->scopes ?: '-'],
        ] : [
            ['提示', '暂无店铺配置，请先完成 OAuth 安装。'],
        ];

        return $content
            ->title('Settings')
            ->description('系统基础配置（V1 只读）')
            ->row(new Table(['配置项', '值'], $rows));
    }
}
