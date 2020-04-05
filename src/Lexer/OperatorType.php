<?php

namespace Ibelousov\MathExec\Lexer;

class OperatorType
{
    const EOF = "EOF";
    const IDENT = "IDENT";
    const NUMBER = "NUMBER";
    const PLUS = "+";
    const MINUS = "-";
    const ASTERISK = "*";
    const SLASH = "/";
    const WDIV = '//';
    const ROOTS = "\\";
    const MODUL = '%';
    const POWER = "^";
    const LT = "<";
    const GT = ">";
    const EQ = "==";
    const NOT_EQ = "!=";
    const GT_OR_EQ = ">=";
    const LT_OR_EQ = "<=";
    const LPAREN = "(";
    const RPAREN = ")";
    const COMMA = ",";
    const BANG = "!";
    const TRUEOP = 'TRUE';
    const FALSEOP = 'FALSE';
}
