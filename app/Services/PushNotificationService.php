<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{

    protected WebPush $webPush;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('push.vapid.subject'),
                'publicKey' => config('push.vapid.public_key'),
                'privateKey' => config('push.vapid.private_key'),
            ]
        ]);
    }

    public function sendToUser(User $user, array $payload): array
    {
        $subscriptions = $user->pushSubscriptions;

        return $this->queueForSubscriptions($subscriptions, $payload);
    }

    public function sendToAllSubscribers(array $payload, ?callable $scope = null)
    {
        $query = PushSubscription::query();

        if ($scope) {
            $scope($query);
        }

        $query->chunkById(1000, function ($subscriptions) use ($payload) {
            if ($subscriptions->isEmpty()) {
                return;
            }

            $this->queueForSubscriptions($subscriptions, $payload);
        });
    }

    /**
     * @param mixed $subscriptions
     * @param array $payload
     * @return array
     * @throws \ErrorException
     * @throws \Random\RandomException
     */
    public function queueForSubscriptions(mixed $subscriptions, array $payload): array
    {
        foreach ($subscriptions as $subscription) {
            $webPushSubscription = Subscription::create([
                'endpoint' => $subscription->endpoint,
                'keys' => [
                    'p256dh' => $subscription->p256dh,
                    'auth' => $subscription->auth
                ]
            ]);

            $this->webPush->queueNotification(
                $webPushSubscription,
                json_encode($payload)
            );
        }

        $results = [];

        foreach ($this->webPush->flush() as $report) {
            $statusCode = $report->getResponse()?->getStatusCode();
            $endPoint = (string)$report->getRequest()->getUri();

            $results[] = [
                'endpoint' => $endPoint,
                'success' => $report->isSuccess(),
                'status' => $statusCode,
                'reason' => $report->isSuccess() ? null : $report->getReason(),
            ];

            if (!$report->isSuccess() && in_array($statusCode, [404, 410])) {
                // Remove the subscription if it is no longer valid
                PushSubscription::where('endpoint', $endPoint)->delete();
            }
        }

        return $results;
    }
}
