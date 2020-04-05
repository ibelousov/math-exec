<?php


namespace Ibelousov\MathExec\Exceptions;

use Exception;
use Throwable;

class TypeMismatchException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Type mismatch exception: " . $message, $code, $previous);
    }
}
