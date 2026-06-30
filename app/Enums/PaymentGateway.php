<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case ZARINPAL = 'ZARINPAL';
    case MELLAT = 'MELLAT';
    case SAMAN = 'SAMAN';
    case INSURANCE_GATEWAY = 'INSURANCE_GATEWAY';
}
