<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFcmNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds between retries

    public function __construct(
        public readonly string $fcmToken,
        public readonly string $title,
        public readonly string $body,
        public readonly array $data = [],
        public readonly ?string $notificationId = null,
    ) {}

    public function handle(): void
    {
        // FCM sending will be implemented in Task 48
        // This is the job skeleton for queue infrastructure
        \Log::info('FCM notification queued', [
            'token' => substr($this->fcmToken, 0, 20) . '...',
            'title' => $this->title,
        ]);
    }

    public function queue(): string
    {
        return 'notifications';
    }
}
