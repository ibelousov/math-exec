<?php


namespace IvanBelousov\MathExec\Ast;


interface NodeInterface
{
    public function TokenLiteral(): string;
    public function String(): string;
}