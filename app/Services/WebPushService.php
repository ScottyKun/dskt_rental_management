<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('webpush.subject'),
                'publicKey' => config('webpush.public_key'),
                'privateKey' => config('webpush.private_key'),
            ],
        ]);
    }

    public function sendToUser(
        int $userId,
        string $title,
        string $body,
        ?string $url = null
    ): void {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();

        foreach ($subscriptions as $subscription) {

            $pushSubscription = Subscription::create([
                'endpoint' => $subscription->endpoint,
                'keys' => [
                    'p256dh' => $subscription->public_key,
                    'auth' => $subscription->auth_token,
                ],
                'contentEncoding' => $subscription->content_encoding ?? 'aes128gcm',
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'url' => $url ?? '/dashboard',
            ]);

            $this->webPush->queueNotification(
                $pushSubscription,
                $payload,
                [
                    'TTL' => 3600,
                    'urgency' => 'normal',
                ]
            );
        }

        foreach ($this->webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                PushSubscription::where('user_id', $userId)
                    ->where('endpoint', $endpoint)
                    ->update([
                        'last_used_at' => now(),
                    ]);

                continue;
            }

            $response = $report->getResponse();

            $statusCode = $response?->getStatusCode();

            logger()->warning('Web Push failed', [
                'user_id' => $userId,
                'endpoint' => $endpoint,
                'status' => $statusCode,
                'reason' => $report->getReason(),
            ]);

            //404 / 410 = subscription définitivement invalide.
            if (in_array($statusCode, [404, 410], true)) {
                PushSubscription::where('user_id', $userId)
                    ->where('endpoint', $endpoint)
                    ->delete();

                logger()->info('Web Push subscription supprimée', [
                    'user_id' => $userId,
                    'endpoint' => $endpoint,
                    'status' => $statusCode,
                ]);
            }
        }
    }
}