<?php

namespace App\Exceptions\Formula;

use RuntimeException;

class FormulaConditionFailedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Formula conditions failed.'
        );
    }
}
