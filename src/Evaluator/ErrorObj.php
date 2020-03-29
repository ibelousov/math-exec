<?php


namespace IvanBelousov\MathExec\Evaluator;


class ErrorObj implements ObjInterface
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function Type(): string
    {
        return ObjType::ERROR_OBJ;
    }

    public function Inspect(): string
    {
        return "ERROR: " . $this->message;
    }
}