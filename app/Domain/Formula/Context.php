<?php

namespace App\Domain\Formula;

class Context
{
    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $result
     */
    public function __construct(
        protected array $input,
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

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->input, $key, $default);
    }

    /**
     * @return array<string, mixed>
     */
    public function result(): array
    {
        return $this->result;
    }
}
