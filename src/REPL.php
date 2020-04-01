<?php

require __DIR__ . '/../vendor/autoload.php';

while( true ) {

    echo ">> ";

    // Read input
    $line = trim( fgets(STDIN) );

    $time = microtime(true);

    try {
        $result = math_exec($line, $argv[1] ?? 40);
    } catch (Ibelousov\MathExec\Exceptions\WrongTokenException $exception) {
        echo "Lexing error: Wrong token\n";

        continue;
    } catch (Ibelousov\MathExec\Exceptions\WrongPrefixOperatorException $exception) {
        echo "Parsing error: Wrong prefix operator\n";

        continue;
    }

    if(is_bool($result))
        echo $result ? 'true' : 'false';
    else
        echo $result;

    echo "\nExecuted in " . (microtime(true) - $time) . " s.";

    echo "\n";
}