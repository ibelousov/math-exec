<?php


namespace Ibelousov\MathExec\Exceptions;

use Exception;
use Throwable;

class NotAFunctionException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("Function does not exists: " . $message, $code, $previous);
    }
}
