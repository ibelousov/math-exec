<?php


namespace Ibelousov\MathExec\Evaluator;

class ReturnValueObj implements ObjInterface
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function Type(): string
    {
        return ObjType::RETURN_VALUE_OBJ;
    }

    public function Inspect(): string
    {
        return $this->value->Inspect();
    }
}
