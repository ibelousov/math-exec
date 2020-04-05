<?php


namespace Ibelousov\MathExec\Exceptions;

use Exception;
use Throwable;

class IdentifierNotFoundException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Identified not found: " . $message, $code, $previous);
    }
}
