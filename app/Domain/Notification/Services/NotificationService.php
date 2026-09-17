<?php

namespace App\Domain\Notification\Services;

use App\Jobs\SendFcmNotificationJob;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create an in-app notification and optionally dispatch FCM push notification.
     */
    public function send(User $recipient, string $type, string $title, string $body, array $data = []): Notification
    {
        $tenantId = $recipient->tenant_id ?? \App\Domain\Tenant\TenantContext::getTenantId();

        $notification = Notification::create([
            'tenant_id'    => $tenantId,
            'user_id'      => $recipient->id,
            'type'         => $type,
            'title'        => $title,
            'body'         => $body,
            'data'         => $data,
            'sent_via_fcm' => false,
        ]);

        if (!empty($recipient->fcm_token)) {
            SendFcmNotificationJob::dispatch(
                $recipient->fcm_token,
                $title,
                $body,
                $data,
                $notification->id
            )->onQueue('notifications');
        }

        return $notification;
    }

    /**
     * Send notification to multiple users.
     *
     * @param iterable<User> $recipients
     */
    public function sendToMany(iterable $recipients, string $type, string $title, string $body, array $data = []): array
    {
        $notifications = [];
        foreach ($recipients as $recipient) {
            $notifications[] = $this->send($recipient, $type, $title, $body, $data);
        }
        return $notifications;
    }
}
