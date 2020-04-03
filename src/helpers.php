<?php

function math_exec($expression, $inner_precision = 40)
{
    $evaluator = new \Ibelousov\MathExec\Evaluator\Evaluator($expression, $inner_precision);

    $value = $evaluator->exec()->value;

    if(is_bool($value)) return $value;

    $trimmed_value = rtrim($value, "0");

    if ($trimmed_value[strlen($trimmed_value) - 1] == '.')
        return substr($trimmed_value, 0, strlen($trimmed_value) - 1);

    return $trimmed_value;
}