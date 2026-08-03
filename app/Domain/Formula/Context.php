<?php

namespace App\Domain\Formula;

class Context
{
    public function __construct(
        protected array $input = [],
        protected array $result = []
    ) {}

    public function input(): array
    {
        return $this->input;
    }

    public function set(string $key, mixed $value): void
    {
        $this->result[$key] = $value;
    }

    public function result(): array
    {
        return $this->result;
    }
}
