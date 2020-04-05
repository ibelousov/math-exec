<?php

namespace Ibelousov\MathExec\Exceptions;

use Exception;

class WrongPrefixOperatorException extends Exception
{
    public function __construct($message = null, $code = 0)
    {
        parent::__construct("Wrong operator: " . $message, $code);
    }
}
