<?php

namespace IvanBelousov\MathExec\Parser;

use IvanBelousov\MathExec\Evaluator\ObjType;
use IvanBelousov\MathExec\Lexer\OperatorType;

class Precedence
{
    const LOWEST = 0;
    const EQUALS = 1;
    const LESSGT = 2;
    const SUM = 3;
    const MOD = 4;
    const PRODUCT= 5;
    const POWERP = 6;
    const PREFIX = 7;
    const CALL = 8;
    const INDEX = 9;

    const precedences = [
        OperatorType::EQ          => self::EQUALS,
        OperatorType::NOT_EQ      => self::EQUALS,
        OperatorType::LT          => self::LESSGT,
        OperatorType::GT          => self::LESSGT,
        OperatorType::LT_OR_EQ    => self::LESSGT,
        OperatorType::GT_OR_EQ    => self::LESSGT,
        OperatorType::PLUS        => self::SUM,
        OperatorType::MINUS       => self::SUM,
        OperatorType::MODUL       => self::MOD,
        OperatorType::SLASH       => self::PRODUCT,
        OperatorType::ASTERISK    => self::PRODUCT,
        OperatorType::WDIV        => self::PRODUCT,
        OperatorType::POWER       => self::POWERP,
        OperatorType::LPAREN      => self::CALL
    ];
}