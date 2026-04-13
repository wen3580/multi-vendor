<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index()
    {
        return Affiliate::query()->latest()->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'email' => ['required', 'email'],
            'referral_code' => ['required', 'string', 'max:100'],
        ]);

        return Affiliate::query()->create($data + ['status' => 'pending']);
    }

    public function approve(Affiliate $affiliate)
    {
        $affiliate->update(['status' => 'approved']);

        return response()->json(['ok' => true, 'status' => $affiliate->status]);
    }

    public function reject(Affiliate $affiliate)
    {
        $affiliate->update(['status' => 'rejected']);

        return response()->json(['ok' => true, 'status' => $affiliate->status]);
    }
}
