<?php


namespace Ibelousov\MathExec\Evaluator;


class NumberObj implements ObjInterface
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function Inspect(): string
    {
        return $this->value;
    }

    public function Type(): string
    {
        return ObjType::NUMBER_OBJ;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}