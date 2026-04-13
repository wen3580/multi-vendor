<?php

namespace App\Services;

use App\Models\Shop;

class ShopifyOAuthService
{
    public function buildInstallUrl(string $shopDomain, string $state): string
    {
        $query = http_build_query([
            'client_id' => config('shopify.api_key'),
            'scope' => implode(',', config('shopify.scopes', [])),
            'redirect_uri' => config('shopify.redirect_uri'),
            'state' => $state,
        ]);

        return "https://{$shopDomain}/admin/oauth/authorize?{$query}";
    }

    public function persistShop(string $shopDomain, string $token, ?string $scopes = null): Shop
    {
        return Shop::query()->updateOrCreate(
            ['shop_domain' => $shopDomain],
            ['access_token' => $token, 'scopes' => $scopes, 'is_active' => true]
        );
    }
}
