<?php


namespace Ibelousov\MathExec\Ast;


interface NodeInterface
{
    public function TokenLiteral(): string;
    public function String(): string;
}