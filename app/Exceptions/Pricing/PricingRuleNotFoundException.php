<?php

namespace App\Exceptions\Pricing;

use Exception;

class PricingRuleNotFoundException extends Exception
{
    public function __construct(int $insuranceTypeId)
    {
        parent::__construct("No active pricing rules found for insurance type ID: {$insuranceTypeId}");
    }
}
