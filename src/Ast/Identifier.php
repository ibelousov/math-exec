<?php


namespace Ibelousov\MathExec\Ast;

class Identifier extends AbstractExpression
{
    public $token;
    public $value;

    public function __construct($token, $value)
    {
        $this->token = $token;
        $this->value = $value;
    }

    public function expressionNode()
    {
    }
    public function TokenLiteral(): string
    {
        return $this->token->literal;
    }

    public function String(): string
    {
        return $this->value;
    }
}
