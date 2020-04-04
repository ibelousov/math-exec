<?php


namespace Ibelousov\MathExec\Exceptions;

use Exception;
use Throwable;

class UnknownOperatorException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unknown operator exception: " . $message, $code, $previous);
    }
}