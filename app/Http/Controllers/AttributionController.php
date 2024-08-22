<?php

namespace App\Http\Controllers;

use App\Models\AffiliateAttribution;
use Illuminate\Http\Request;

class AttributionController extends Controller
{
    public function index(Request $request)
    {
        return AffiliateAttribution::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('affiliate_id'), fn ($q) => $q->where('affiliate_id', $request->integer('affiliate_id')))
            ->when($request->filled('attribution_type'), fn ($q) => $q->where('attribution_type', $request->string('attribution_type')))
            ->when($request->filled('order_id'), fn ($q) => $q->where('order_id', $request->integer('order_id')))
            ->latest('attributed_at')
            ->paginate($request->integer('per_page', 20));
    }

    public function show(AffiliateAttribution $attribution)
    {
        return $attribution;
    }

    public function reassign(Request $request, AffiliateAttribution $attribution)
    {
        $data = $request->validate([
            'affiliate_id' => ['required', 'integer'],
            'attribution_type' => ['nullable', 'in:coupon,cookie,manual'],
        ]);

        $attribution->update([
            'affiliate_id' => $data['affiliate_id'],
            'attribution_type' => $data['attribution_type'] ?? 'manual',
            'attributed_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function remove(AffiliateAttribution $attribution)
    {
        $attribution->delete();

        return response()->json(['ok' => true]);
    }
}
