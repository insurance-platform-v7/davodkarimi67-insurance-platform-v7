<?php

namespace App\Services\Notification;

use App\Mail\ClaimNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sms(
        string $mobile,
        string $message
    ): bool {
        Log::info('sms.notification', [
            'mobile_hash' => substr(hash('sha256', $mobile), 0, 12),
            'message_length' => strlen($message),
        ]);

        return true;
    }

    public function email(
        string $email,
        string $subject,
        string $message
    ): bool {
        Mail::to($email)->send(
            new ClaimNotificationMail(
                $subject,
                $message
            )
        );

        return true;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function send(
        string $channel,
        array $data
    ): bool {
        return match (strtolower($channel)) {
            'sms' => $this->sms(
                $data['mobile'] ?? '',
                $data['message'] ?? ''
            ),

            'email' => $this->email(
                $data['email'] ?? '',
                $data['subject'] ?? '',
                $data['message'] ?? ''
            ),

            default => false,
        };
    }
}
