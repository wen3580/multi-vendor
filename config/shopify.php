<?php

return [
    'app_name' => env('SHOPIFY_APP_NAME', 'Affiliate App'),
    'api_key' => env('SHOPIFY_API_KEY'),
    'api_secret' => env('SHOPIFY_API_SECRET'),
    'scopes' => explode(',', env('SHOPIFY_API_SCOPES', 'read_orders,read_customers,read_products,read_discounts,write_discounts')),
    'redirect_uri' => env('SHOPIFY_REDIRECT_URI'),
    'webhook_secret' => env('SHOPIFY_WEBHOOK_SECRET'),
    'api_version' => env('SHOPIFY_API_VERSION', '2026-01'),
];
