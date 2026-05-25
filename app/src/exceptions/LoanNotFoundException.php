<?php

namespace App\Exceptions;

use RuntimeException;

class LoanNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Loan not found.')
    {
        parent::__construct($message);
    }
}
