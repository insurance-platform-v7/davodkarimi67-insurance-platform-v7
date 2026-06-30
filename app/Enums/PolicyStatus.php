<?php
// File: app/Enums/PolicyStatus.php

namespace App\Enums;

enum PolicyStatus: string
{
    case QUOTE_CREATED = 'quote_created';
    case UNDERWRITING_PENDING = 'underwriting_pending';
    case PAYMENT_PENDING = 'payment_pending';
    case PAID = 'paid';
    case ISSUED = 'issued';
    case CANCELED = 'canceled';
    case REJECTED = 'rejected';
}
