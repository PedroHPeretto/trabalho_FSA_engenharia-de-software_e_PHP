<?php

namespace App\Exceptions;

use RuntimeException;

class PendingFineException extends RuntimeException
{
    public function __construct(string $message = 'You have unpaid fines. Please settle them before proceeding.')
    {
        parent::__construct($message);
    }
}
