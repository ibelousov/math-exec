<?php


namespace Ibelousov\MathExec\Ast;

class InfixExpression extends AbstractExpression
{
    public $token;
    public $left;
    public $operator;
    public $right;

    public function __construct($token, $operator, $left)
    {
        $this->token = $token;
        $this->operator = $operator;
        $this->left = $left;
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
        $out = '';

        $out = "(";
        $out .= $this->left->String();
        $out .= " " . $this->operator . " ";
        $out .= $this->right->String();
        $out .= ")";

        return $out;
    }
}
