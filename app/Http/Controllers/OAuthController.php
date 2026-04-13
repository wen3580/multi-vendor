<?php

namespace App\Http\Controllers;

use App\Services\ShopifyOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    public function __construct(private readonly ShopifyOAuthService $oauthService)
    {
    }

    public function install(Request $request)
    {
        $shop = (string) $request->query('shop');
        abort_if($shop === '', 422, 'Missing shop parameter.');

        $state = Str::random(32);
        $request->session()->put('shopify_oauth_state', $state);

        return redirect()->away($this->oauthService->buildInstallUrl($shop, $state));
    }

    public function callback(Request $request)
    {
        $state = (string) $request->query('state');
        abort_if($state !== (string) $request->session()->pull('shopify_oauth_state'), 419, 'Invalid OAuth state.');

        return response()->json([
            'message' => 'OAuth callback received. Implement code->token exchange here.',
            'shop' => $request->query('shop'),
            'code' => $request->query('code'),
        ]);
    }
}
