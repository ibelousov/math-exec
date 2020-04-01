<?php

require 'vendor/autoload.php';

$a = 4.123412341234234;
$b = 2323423423412344;

$time = microtime(true);

echo place_parenthesis("4 + 4 * 4 + 2 * 2", true);

echo " - " . (microtime(true) - $time);