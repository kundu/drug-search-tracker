<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct(Exception $previous = null, $headers = [], $code = 0)
    {
        parent::__construct(
            "Invalid credentials",
            401,
            $previous,
        );
    }
}
