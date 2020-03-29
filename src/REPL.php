<?php

require __DIR__ . '/../vendor/autoload.php';

use IvanBelousov\MathExec\Evaluator\Evaluator;
use IvanBelousov\MathExec\Lexer\Lexer;
use IvanBelousov\MathExec\Parser\Parser;

while( true ) {

    echo ">> ";

    // Read user choice
    $line = trim( fgets(STDIN) );

    $a = new IvanBelousov\MathExec\Lexer\Lexer($line);
    $b = new IvanBelousov\MathExec\Parser\Parser($a);
    $c = new IvanBelousov\MathExec\Evaluator\Evaluator($b);

    echo $c->exec();

    echo "\n";
}
