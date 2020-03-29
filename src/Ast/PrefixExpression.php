<?php


namespace IvanBelousov\MathExec\Ast;


class PrefixExpression extends AbstractExpression
{
    public $token;
    public $operator;
    public $right;

    public function __construct($token, $literal = null)
    {
        $this->token = $token;
        $this->operator = $literal;
    }

    public function expressionNode() {}
    public function TokenLiteral(): string
    {
        return $this->token->literal;
    }
    public function String(): string
    {
        $out = '';

        $out = '(';
        $out .= $this->operator;
        $out .= $this->right->String();
        $out .= ")";

        return $out;
    }
}