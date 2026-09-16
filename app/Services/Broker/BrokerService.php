<?php

namespace App\Services\Broker;

use App\Models\Broker;
use Illuminate\Database\Eloquent\Collection;

class BrokerService
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Broker
    {
        return Broker::query()->create($data);
    }

    /**
     * @return Collection<int, Broker>
     */
    public function active(): Collection
    {
        return Broker::query()
            ->where('is_active', true)
            ->get();
    }
}
