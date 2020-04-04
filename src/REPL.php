<?php

require __DIR__ . '/../vendor/autoload.php';

while( true ) {

    echo ">> ";

    // Read input
    $line = trim( fgets(STDIN) );

    if(strtolower(trim($line)) == 'exit')
        break;

    $time = microtime(true);

    try {
        if(isset($argv[1]))
            $result = \Ibelousov\MathExec\Evaluator\Evaluator::math_exec($line, $argv[1]);
        else
            $result = \Ibelousov\MathExec\Evaluator\Evaluator::math_exec($line);
    } catch (Exception $exception) {
        echo $exception->getMessage() . "\n";

        continue;
    }

    echo $result;

    $seconds = microtime(true) - $time;

    echo "\nExecuted in {$seconds}s.";

    echo "\n";
}
