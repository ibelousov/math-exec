<?php


namespace IvanBelousov\MathExec\Parser;


interface PrefixParseFnInterface
{
    public function prefixParseFn(): ?Expression;
}