<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCouponCode;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        return AffiliateCouponCode::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('affiliate_id'), fn ($q) => $q->where('affiliate_id', $request->integer('affiliate_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'affiliate_id' => ['required', 'integer'],
            'discount_node_gid' => ['required', 'string'],
            'code' => ['required', 'string', 'max:100'],
            'discount_type' => ['required', 'in:percent,fixed,shipping'],
            'discount_value' => ['required', 'numeric'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        return AffiliateCouponCode::query()->create($data);
    }

    public function disable(AffiliateCouponCode $coupon)
    {
        $coupon->update(['status' => 'inactive']);

        return response()->json(['ok' => true]);
    }

    public function enable(AffiliateCouponCode $coupon)
    {
        $coupon->update(['status' => 'active']);

        return response()->json(['ok' => true]);
    }
}
