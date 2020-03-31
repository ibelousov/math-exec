<?php

require 'vendor/autoload.php';

$lexer = new \IvanBelousov\MathExec\Lexer\Lexer('3 + b');

while($token = $lexer->nextToken())
    if($token->isEof())
        break;