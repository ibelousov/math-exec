<?php


namespace IvanBelousov\MathExec\Evaluator;


class BuiltinFunctionObj implements ObjInterface
{
    public $builtinFunction;

    public function __construct($builtinFunction)
    {
        $this->builtinFunction = $builtinFunction;
    }

    public function Type(): string
    {
        return ObjType::BUILTIN_OBJ;
    }

    public function Inspect(): string
    {
        return "builtin function";
    }
}