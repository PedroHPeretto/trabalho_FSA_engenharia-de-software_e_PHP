<?php

namespace App\Exceptions;

use RuntimeException;

class RenewalBlockedException extends RuntimeException
{
    public function __construct(string $message = 'Cannot renew: book has pending reservations.')
    {
        parent::__construct($message);
    }
}
