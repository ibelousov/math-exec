<?php

require_once "Token.php";

interface Node {
    public function TokenLiteral(): string;
    public function String(): string;
}

abstract class Statement implements Node {
    abstract public function TokenLiteral(): string;
    abstract public function statementNode();
}

abstract class Expression implements Node {
    abstract public function TokenLiteral(): string;
    abstract public function expressionNode();
}

class Program implements Node
{
    public $statements;

    public function TokenLiteral(): string 
    {
        if(count($this->statements) > 0)
            return $this->statements[0]->TokenLiteral();

        return "";
    }

    public function String(): string
    {
        $out = [];

        foreach($this->statements as $statement) {
            $out[] = $statement->String();
        }

        return implode("", $out);
    }
}

class ExpressionStatement extends Statement
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

class NumberLiteral extends Expression
{
    public $token;
    public $value;

    public function __construct($token, $value = null)
    {
        $this->token = $token;
        $this->value = $value;
    }

    public function expressionNode() {}
    public function TokenLiteral(): string
    {
        return $this->token->literal;
    }
    public function String(): string
    {
        return $this->TokenLiteral();
    }
}

class PrefixExpression extends Expression
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

class InfixExpression extends Expression
{
    public $token;
    public $left;
    public $operator;
    public $right;

    public function InfixExpression($token, $operator, $left)
    {
        $this->token = $token;
        $this->operator = $operator;
        $this->left = $left;
    }

    public function expressionNode() {}
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

class Boolean extends Expression
{
    public $token;
    public $bool;

    public function __construct($token, $value)
    {
        $this->token = $token;
        $this->bool = $value;
    }

    public function expressionNode() {}
    public function TokenLiteral(): string 
    {
        return $this->token->literal;
    }
    public function String(): string
    {
        return $this->token->literal;
    }
}

class CallExpression extends Expression
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

class Identifier extends Expression
{
    public $token;
    public $value;

    public function __construct($token, $value)
    {
        $this->token = $token;
        $this->value = $value;
    }

    public function expressionNode(){}
    public function TokenLiteral(): string
    {
        return $this->token->literal;
    }

    public function String(): string
    {
        return $this->value;
    }
}