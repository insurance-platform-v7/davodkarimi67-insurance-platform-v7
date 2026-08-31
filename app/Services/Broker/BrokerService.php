<?php

namespace App\Services\Broker;

use App\Models\Broker;
use Illuminate\Database\Eloquent\Collection;

class BrokerService
{
    public function create(array $data): Broker
    {
        return Broker::query()->create($data);
    }

    public function active(): Collection
    {
        return Broker::query()
            ->where('is_active', true)
            ->get();
    }
}
