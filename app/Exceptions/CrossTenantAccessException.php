<?php

namespace App\Exceptions;

use Exception;

class CrossTenantAccessException extends Exception
{
    protected $message = 'Cross tenant access detected.';
}
