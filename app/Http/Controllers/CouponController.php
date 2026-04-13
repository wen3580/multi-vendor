<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCouponCode;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return AffiliateCouponCode::query()->latest()->paginate();
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'affiliate_id' => ['required', 'integer'],
            'discount_node_gid' => ['required', 'string'],
            'code' => ['required', 'string', 'max:100'],
            'discount_type' => ['required', 'in:percent,fixed,shipping'],
            'discount_value' => ['required', 'numeric'],
        ]);

        return AffiliateCouponCode::query()->create($data);
    }

    public function disable(AffiliateCouponCode $coupon)
    {
        $coupon->update(['status' => 'inactive']);

        return response()->json(['ok' => true]);
    }
}
