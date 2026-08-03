<?php

namespace App\Exceptions\Formula;

use RuntimeException;

class FormulaVersionNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Active formula version not found.'
        );
    }
}
