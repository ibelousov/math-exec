<?php


namespace Ibelousov\MathExec\Ast;

class Boolean extends AbstractExpression
{
    public $token;
    public $bool;

    public function __construct($token, $value)
    {
        $this->token = $token;
        $this->bool = $value;
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
        return $this->token->literal;
    }
}
