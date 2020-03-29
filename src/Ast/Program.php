<?php


namespace IvanBelousov\MathExec\Ast;


class Program implements NodeInterface
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