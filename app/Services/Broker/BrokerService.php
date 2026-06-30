<?php

namespace App\Services\Broker;

use App\Models\Broker;

class BrokerService
{
    public function create(array $data): Broker
    {
        return Broker::create($data);
    }

    public function active()
    {
        return Broker::query()
            ->where('is_active', true)
            ->get();
    }
}
