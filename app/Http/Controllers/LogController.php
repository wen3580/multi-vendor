<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function webhooks(Request $request)
    {
        return WebhookLog::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('process_status'), fn ($q) => $q->where('process_status', $request->string('process_status')))
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function tracking(Request $request)
    {
        return AffiliateClick::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('affiliate_id'), fn ($q) => $q->where('affiliate_id', $request->integer('affiliate_id')))
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function errors(Request $request)
    {
        return WebhookLog::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->whereNotNull('error_message')
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }
}
