<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        return Affiliate::query()
            ->when($request->filled('shop_id'), fn ($q) => $q->where('shop_id', $request->integer('shop_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('email', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('referral_code', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 20));
    }

    public function show(Affiliate $affiliate)
    {
        return $affiliate;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_id' => ['required', 'integer'],
            'email' => ['required', 'email'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'referral_code' => ['required', 'string', 'max:100'],
            'referral_slug' => ['nullable', 'string', 'max:150'],
            'default_discount_code' => ['nullable', 'string', 'max:100'],
            'commission_type' => ['nullable', 'in:percent,fixed'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
            'payout_method' => ['nullable', 'in:paypal,bank,manual'],
            'payout_account' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        return Affiliate::query()->create($data + ['status' => 'pending']);
    }

    public function update(Request $request, Affiliate $affiliate)
    {
        $data = $request->validate([
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:pending,approved,rejected,blocked'],
            'referral_code' => ['nullable', 'string', 'max:100'],
            'referral_slug' => ['nullable', 'string', 'max:150'],
            'default_discount_code' => ['nullable', 'string', 'max:100'],
            'commission_type' => ['nullable', 'in:percent,fixed'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
            'payout_method' => ['nullable', 'in:paypal,bank,manual'],
            'payout_account' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $affiliate->update($data);

        return $affiliate->fresh();
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

    public function block(Affiliate $affiliate)
    {
        $affiliate->update(['status' => 'blocked']);

        return response()->json(['ok' => true, 'status' => $affiliate->status]);
    }
}
