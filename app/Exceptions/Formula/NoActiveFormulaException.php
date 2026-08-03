<?php

namespace App\Exceptions\Formula;

use RuntimeException;

class NoActiveFormulaException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'No active formula assigned to company product.'
        );
    }
}
