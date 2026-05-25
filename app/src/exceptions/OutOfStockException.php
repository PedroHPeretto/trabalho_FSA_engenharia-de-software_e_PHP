<?php

namespace App\Exceptions;

use RuntimeException;

class OutOfStockException extends RuntimeException
{
    public function __construct(string $message = 'This book is currently out of stock.')
    {
        parent::__construct($message);
    }
}
