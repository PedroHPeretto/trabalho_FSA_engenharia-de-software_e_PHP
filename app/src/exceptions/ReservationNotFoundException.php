<?php

namespace App\Exceptions;

use RuntimeException;

class ReservationNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Reservation not found.')
    {
        parent::__construct($message);
    }
}
