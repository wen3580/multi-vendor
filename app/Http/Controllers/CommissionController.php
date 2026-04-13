<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        return AffiliateCommission::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('affiliate_id'), fn ($q) => $q->where('affiliate_id', $request->integer('affiliate_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('order_id'), fn ($q) => $q->where('order_id', $request->integer('order_id')))
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function show(AffiliateCommission $commission)
    {
        return $commission;
    }

    public function approve(AffiliateCommission $commission)
    {
        $commission->update(['status' => 'approved', 'approved_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function void(AffiliateCommission $commission)
    {
        $commission->update(['status' => 'void']);

        return response()->json(['ok' => true]);
    }

    public function markPaid(AffiliateCommission $commission)
    {
        $commission->update(['status' => 'paid', 'paid_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function recalculate(AffiliateCommission $commission)
    {
        $amount = $commission->commission_type === 'percent'
            ? round(($commission->commission_base_amount * $commission->commission_value) / 100, 2)
            : $commission->commission_value;

        $commission->update(['commission_amount' => $amount]);

        return response()->json(['ok' => true, 'commission_amount' => $amount]);
    }
}
