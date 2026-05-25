<?php

namespace App\Exceptions;

use RuntimeException;

class UserBlockedException extends RuntimeException
{
    public function __construct(string $message = 'Your account has been blocked due to overdue fines.')
    {
        parent::__construct($message);
    }
}
