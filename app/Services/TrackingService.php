<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateClick;
use Illuminate\Support\Str;

class TrackingService
{
    public function recordClick(string $shopDomain, string $refCode, array $meta = []): ?AffiliateClick
    {
        $affiliate = Affiliate::query()
            ->where('referral_code', $refCode)
            ->whereHas('shop', fn ($query) => $query->where('shop_domain', $shopDomain))
            ->first();

        if (!$affiliate) {
            return null;
        }

        return AffiliateClick::query()->create([
            'shop_id' => $affiliate->shop_id,
            'affiliate_id' => $affiliate->id,
            'click_uuid' => (string) Str::uuid(),
            'ref_code' => $refCode,
            'landing_url' => $meta['landing_url'] ?? null,
            'referer' => $meta['referer'] ?? null,
            'utm_source' => $meta['utm_source'] ?? null,
            'utm_medium' => $meta['utm_medium'] ?? null,
            'utm_campaign' => $meta['utm_campaign'] ?? null,
            'ip' => $meta['ip'] ?? null,
            'user_agent' => $meta['user_agent'] ?? null,
            'browser_cookie_id' => $meta['browser_cookie_id'] ?? null,
        ]);
    }
}
