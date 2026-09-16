<?php

namespace App\Listeners;

class SendQuoteOfferNotification
{
    public int $tries = 3;

    public int $backoff = 60;

    // بقیه کد فعلی کلاس بدون تغییر
}
