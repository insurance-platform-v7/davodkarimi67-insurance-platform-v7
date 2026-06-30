<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sms(
        string $mobile,
        string $message
    ): bool {

        Log::info('sms.notification', [
            'mobile' => $mobile,
            'message' => $message,
        ]);

        return true;
    }

    public function email(
        string $email,
        string $subject,
        string $message
    ): bool {

        Mail::raw(
            $message,
            function ($mail) use (
                $email,
                $subject
            ) {
                $mail->to($email)
                    ->subject($subject);
            }
        );

        return true;
    }

    public function send(
        string $channel,
        array $data
    ): bool {

        return match (strtolower($channel)) {

            'sms' => $this->sms(
                $data['mobile'],
                $data['message']
            ),

            'email' => $this->email(
                $data['email'],
                $data['subject'],
                $data['message']
            ),

            default => false,
        };
    }
}
