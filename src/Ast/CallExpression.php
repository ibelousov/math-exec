<?php


namespace IvanBelousov\MathExec\Ast;


class CallExpression extends AbstractExpression
{
    public $token;
    public $function;
    public $arguments;

    public function __construct($token, $function, $arguments = null)
    {
        $this->token = $token;
        $this->function = $function;
        $this->arguments = $arguments;
    }

    public function expressionNode() {}
    public function TokenLiteral(): string
    {
        return $this->token->literal;
    }
    public function String(): string
    {
        $out = '';

        $args = [];
        foreach((array)$this->arguments as $argument)
            $args[] = $argument->String();

        $out .= $this->function->String();
        $out .= '(';
        $out .= implode($args, ', ');
        $out .= ')';

        return $out;
    }
}