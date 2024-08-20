<?php

namespace App\Services;

class WebhookVerifyService
{
    public function verify(string $body, ?string $hmacHeader): bool
    {
        if (!$hmacHeader) {
            return false;
        }

        $calculated = base64_encode(hash_hmac('sha256', $body, (string) config('shopify.webhook_secret'), true));

        return hash_equals($calculated, $hmacHeader);
    }
}
