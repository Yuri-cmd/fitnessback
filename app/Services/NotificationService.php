<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(private readonly Messaging $messaging) {}

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return;
        }

        $notification = Notification::create($title, $body);

        $messages = array_map(
            fn($token) => CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data),
            $tokens
        );

        foreach ($messages as $message) {
            try {
                $this->messaging->send($message);
            } catch (\Throwable $e) {
                Log::error('FCM send error: ' . $e->getMessage());
            }
        }
    }

    public function sendToAll(string $title, string $body, array $data = []): void
    {
        $tokens = \App\Models\FcmToken::pluck('token')->toArray();

        if (empty($tokens)) {
            return;
        }

        $notification = Notification::create($title, $body);
        $messages = array_map(
            fn($token) => CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data),
            $tokens
        );

        try {
            $this->messaging->sendAll($messages);
        } catch (\Throwable $e) {
            Log::error('FCM broadcast error: ' . $e->getMessage());
        }
    }
}
