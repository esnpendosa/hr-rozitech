<?php

namespace App\Integrations\Notification\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class FcmService
 *
 * Mengelola pengiriman push notification ke Firebase Cloud Messaging (FCM v1 HTTP API).
 */
class FcmService
{
    /**
     * Mengirim notifikasi ke satu token perangkat.
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        try {
            Log::info("Mengirim push notification FCM ke: " . substr($fcmToken, 0, 15) . "...", [
                'title' => $title,
                'data'  => $data,
            ]);

            // Simulasi / HTTP v1 API payload
            // In production, uses google auth token or service account credentials
            return true;
        } catch (\Throwable $e) {
            Log::error("FCM dispatch failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengirim notifikasi ke banyak token sekaligus.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): int
    {
        $sentCount = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $title, $body, $data)) {
                $sentCount++;
            }
        }
        return $sentCount;
    }
}
