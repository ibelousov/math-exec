<?php


namespace IvanBelousov\MathExec\Evaluator;


interface ObjInterface
{
    public function Type(): string;
    public function Inspect(): string;
}