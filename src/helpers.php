<?php

function math_exec($expression, $inner_precision = 40)
{
    $lexer = new \Ibelousov\MathExec\Lexer\Lexer($expression);
    $parser= new \Ibelousov\MathExec\Parser\Parser($lexer);
    $evaluator = new \Ibelousov\MathExec\Evaluator\Evaluator($parser, $inner_precision);

    $value = $evaluator->exec()->value;

    if(is_bool($value)) return $value;

    $trimmed_value = rtrim($value, "0");

    if ($trimmed_value[strlen($trimmed_value) - 1] == '.')
        return substr($trimmed_value, 0, strlen($trimmed_value) - 1);

    return $trimmed_value;
}

function place_parenthesis($expression, $cutOuter = false)
{
    $lexer = new \Ibelousov\MathExec\Lexer\Lexer($expression);
    $parser= new \Ibelousov\MathExec\Parser\Parser($lexer);

    $result = $parser->parseProgram()->String();

    if($cutOuter)
        return substr($result, 1, strlen($result) - 2);

    return $result;
}