<?php

namespace App\Repositories\Customer;

use App\Models\Customer;

class CustomerRepository
{
    public function findForTenantOrFail(
        int $customerId,
        int $tenantId
    ): Customer {
        return Customer::query()
            ->whereKey($customerId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();
    }
}
