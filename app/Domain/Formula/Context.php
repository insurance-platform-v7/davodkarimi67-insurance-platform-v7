<?php

namespace App\Domain\Formula;

class Context
{
    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $result
     */
    public function __construct(
        protected array $input = [],
        protected array $result = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function input(): array
    {
        return $this->input;
    }

    public function set(string $key, mixed $value): void
    {
        $this->result[$key] = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function result(): array
    {
        return $this->result;
    }
}
