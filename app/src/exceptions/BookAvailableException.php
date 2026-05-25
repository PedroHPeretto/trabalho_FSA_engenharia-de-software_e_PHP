<?php

namespace App\Exceptions;

use RuntimeException;

class BookAvailableException extends RuntimeException
{
    public function __construct(string $message = 'Book is available, please loan it directly.')
    {
        parent::__construct($message);
    }
}
