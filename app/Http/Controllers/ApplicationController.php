<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        return AffiliateApplication::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%'.$request->string('email').'%'))
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function show(AffiliateApplication $application)
    {
        return $application;
    }

    public function approve(Request $request, AffiliateApplication $application)
    {
        $data = $request->validate([
            'reviewed_by' => ['nullable', 'integer'],
            'create_coupon' => ['nullable', 'boolean'],
            'use_default_commission' => ['nullable', 'boolean'],
        ]);

        return DB::transaction(function () use ($application, $data) {
            $affiliateData = [
                'shop_id' => $application->shop_id,
                'email' => $application->email,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'phone' => $application->phone,
                'status' => 'approved',
                'referral_code' => strtoupper(Str::random(8)),
                'notes' => $application->note,
            ];

            if (!($data['use_default_commission'] ?? true)) {
                $affiliateData['commission_type'] = 'percent';
                $affiliateData['commission_value'] = 10;
            }

            $affiliate = Affiliate::query()->firstOrCreate(
                ['shop_id' => $application->shop_id, 'email' => $application->email],
                $affiliateData
            );

            $application->update([
                'status' => 'approved',
                'reviewed_by' => $data['reviewed_by'] ?? null,
                'reviewed_at' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'application_id' => $application->id,
                'affiliate_id' => $affiliate->id,
                'create_coupon' => (bool) ($data['create_coupon'] ?? false),
            ]);
        });
    }

    public function reject(Request $request, AffiliateApplication $application)
    {
        $data = $request->validate([
            'reviewed_by' => ['nullable', 'integer'],
        ]);

        $application->update([
            'status' => 'rejected',
            'reviewed_by' => $data['reviewed_by'] ?? null,
            'reviewed_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}
