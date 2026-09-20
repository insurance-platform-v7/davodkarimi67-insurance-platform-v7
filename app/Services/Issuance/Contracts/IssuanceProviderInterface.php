<?php

// File: app/Services/Issuance/Contracts/IssuanceProviderInterface.php

namespace App\Services\Issuance\Contracts;

use App\Models\Policy;

interface IssuanceProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function issue(Policy $policy): array;
}
