<?php

function math_exec($expression, $inner_precision = 40)
{
    $evaluator = new \Ibelousov\MathExec\Evaluator\Evaluator($expression, $inner_precision);

    return $evaluator->exec()->value;
}