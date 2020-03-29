<?php


namespace IvanBelousov\MathExec\Parser;


interface InfixParseFnInterface
{
    public function infixParseFn(Expression $exp): ?Expression;
}