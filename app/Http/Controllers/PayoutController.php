<?php

namespace App\Http\Controllers;

use App\Models\AffiliatePayout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        return AffiliatePayout::query()
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:10'],
            'method' => ['nullable', 'in:paypal,bank,manual'],
            'note' => ['nullable', 'string'],
        ]);

        $data['payout_no'] = 'PO-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

        return AffiliatePayout::query()->create($data);
    }

    public function show(AffiliatePayout $payout)
    {
        return $payout;
    }

    public function export(AffiliatePayout $payout)
    {
        return response()->json([
            'ok' => true,
            'payout_no' => $payout->payout_no,
            'record' => [
                'affiliate_id' => $payout->affiliate_id,
                'amount' => $payout->amount,
                'currency' => $payout->currency,
                'method' => $payout->method,
                'status' => $payout->status,
            ],
        ]);
    }

    public function markPaid(Request $request, AffiliatePayout $payout)
    {
        $data = $request->validate(['note' => ['nullable', 'string']]);

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'note' => $data['note'] ?? $payout->note,
        ]);

        return response()->json(['ok' => true]);
    }
}
