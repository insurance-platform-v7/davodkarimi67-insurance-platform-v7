<?php

namespace Tests\Feature;

use App\Services\Notification\NotificationService;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_sms_notification()
    {
        $service = app(
            NotificationService::class
        );

        $result = $service->sms(
            '09120000000',
            'Test Message'
        );

        $this->assertTrue($result);
    }

    public function test_email_notification()
    {
        $service = app(
            NotificationService::class
        );

        $result = $service->send(
            'email',
            [
                'email' => 'test@example.com',
                'subject' => 'Test',
                'message' => 'Hello',
            ]
        );

        $this->assertTrue($result);
    }
}
