<?php


namespace Ibelousov\MathExec\Ast;

abstract class AbstractStatement implements NodeInterface
{
    abstract public function TokenLiteral(): string;
    abstract public function statementNode();
}
