<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderWebhookJob;
use App\Models\WebhookLog;
use App\Services\WebhookVerifyService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(Request $request, WebhookVerifyService $verifyService)
    {
        $payload = (string) $request->getContent();
        $topic = (string) $request->header('X-Shopify-Topic', 'unknown');
        $hmac = $request->header('X-Shopify-Hmac-Sha256');
        $hmacValid = $verifyService->verify($payload, $hmac);

        $log = WebhookLog::query()->create([
            'topic' => $topic,
            'webhook_id' => $request->header('X-Shopify-Webhook-Id'),
            'payload' => $payload,
            'hmac_valid' => $hmacValid,
            'process_status' => $hmacValid ? 'pending' : 'ignored',
        ]);

        if (!$hmacValid) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        if ($topic === 'orders/create') {
            ProcessOrderWebhookJob::dispatch($log->id);
        }

        return response()->json(['ok' => true]);
    }
}
