<?php

namespace App\Jobs;

use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BroadcastPushNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $payload, public ?string $segment = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PushNotificationService $notificationService): void
    {
        $scope = null;

        if ($this->segment === 'pumpers') {
            $scope = function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('role', 'Pumper');
                });
            };
        }

//        $notificationService->sendToAllSubscribers($this->payload, $scope);
        $notificationService->sendToAllSubscribers($this->payload, $scope ?? null);
    }
}
