<?php


namespace IvanBelousov\MathExec\Ast;


abstract class AbstractExpression implements NodeInterface {
    abstract public function TokenLiteral(): string;
    abstract public function expressionNode();
}