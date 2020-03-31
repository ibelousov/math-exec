<?php


namespace Ibelousov\MathExec\Evaluator;


class BooleanObj implements ObjInterface
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function Type(): string
    {
        return ObjType::BOOLEAN_OBJ;
    }

    public function Inspect(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value ? '1' : '0';
    }
}