#!/usr/bin/php
<?php

require_once 'Lexer.php';
require_once 'Parser.php';
require_once 'Environment.php';
require_once 'Evaluator.php';

$env = new Environment();

while( true ) {
    
    // Print the menu on console
    echo ">> ";

    // Read user choice
    $line = trim( fgets(STDIN) );
    $lexer = new Lexer($line);
    $parser = new Parser($lexer);

    $program = $parser->parseProgram();

    $evaluated = Evaluator::EvalObj($program, $env);

    if($evaluated != null) {
        echo $evaluated->Inspect();
    }
    echo "\n";
}
