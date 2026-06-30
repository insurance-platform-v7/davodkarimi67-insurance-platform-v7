<?php
// File: app/Services/Issuance/Contracts/IssuanceProviderInterface.php

namespace App\Services\Issuance\Contracts;

use App\Models\Policy;

interface IssuanceProviderInterface
{
    public function issue(Policy $policy): array;
}
