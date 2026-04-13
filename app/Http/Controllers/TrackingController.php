<?php

namespace App\Http\Controllers;

use App\Services\TrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function click(Request $request, TrackingService $trackingService)
    {
        $data = $request->validate([
            'shop' => ['required', 'string'],
            'ref_code' => ['required', 'string'],
            'landing_url' => ['nullable', 'url'],
        ]);

        $click = $trackingService->recordClick($data['shop'], $data['ref_code'], [
            'landing_url' => $data['landing_url'] ?? null,
            'referer' => $request->headers->get('referer'),
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser_cookie_id' => $request->input('browser_cookie_id'),
        ]);

        if (!$click) {
            return response()->json(['message' => 'Invalid ref code'], 404);
        }

        return response()->json([
            'click_uuid' => $click->click_uuid,
            'affiliate_id' => $click->affiliate_id,
        ]);
    }
}
