<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;

class CommissionController extends Controller
{
    public function index()
    {
        return AffiliateCommission::query()->latest()->paginate();
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
}
