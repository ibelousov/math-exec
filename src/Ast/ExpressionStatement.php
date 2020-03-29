<?php


namespace IvanBelousov\MathExec\Ast;


class ExpressionStatement extends AbstractStatement
{
    public $token;
    public $expression;

    public function __construct($token, $expression = null)
    {
        $this->token = $token;
    }
    public function statementNode() {}
    public function TokenLiteral(): string
    {
        return $this->token->literal;
    }

    public function String(): string
    {
        if($this->expression != null) {
            return $this->expression->String();
        }

        return "";
    }
}