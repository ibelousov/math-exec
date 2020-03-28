<?php


const NUMBER_OBJ = "INTEGER";
const BOOLEAN_OBJ = "BOOLEAN";
const NIL_OBJ = "NIL";
const ERROR_OBJ = "ERROR";
const BUILIN_OBJ = "BUILIN";

interface Obj
{
    public function Type(): string;
    public function Inspect(): string;
} 

class NumberObj implements Obj
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
        return NUMBER_OBJ;
    }
}

class BooleanObj implements Obj
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function Type(): string
    {
        return BOOLEAN_OBJ;
    }

    public function Inspect(): string
    {
        return $this->value;
    }
}

class NIL implements Obj
{
    public function Type(): string
    {
        return NIL_OBJ;
    }

    public function Inspect(): string
    {
        return "nil";
    }
}

class ReturnValueObj implements Obj
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function Type(): string
    {
        return RETURN_VALUE_OBJ;
    }

    public function Inspect(): string
    {
        return $this->value->Inspect();
    }
}

class ErrorObj implements Obj
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function Type(): string
    {
        return ERROR_OBJ;
    }

    public function Inspect(): string
    {
        return "ERROR: " . $this->message;
    }
}

class BuiltinFunctionObj implements Obj
{
    public $builtinFunction;

    public function __construct($builtinFunction)
    {
        $this->builtinFunction = $builtinFunction;
    }

    public function Type(): string
    {
        return BUILIN_OBJ;
    }

    public function Inspect(): string
    {
        return "builtin function";
    }
}