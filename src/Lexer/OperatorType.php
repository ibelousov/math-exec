<?php

namespace Ibelousov\MathExec\Lexer;

class OperatorType
{
    const ILLEGAL = "ILLEGAL";
    const EOF = "EOF";
    const IDENT = "IDENT";
    const NUMBER = "NUMBER";
    const ASSIGN = "=";
    const PLUS = "+";
    const COMMA = ",";
    const MINUS = "-";
    const BANG = "!";
    const ASTERISK = "*";
    const SLASH = "/";
    const WDIV = '//';
    const ROOTS = "\\";
    const MODUL = '%';
    const POWER = "^";
    const DIV_WITHOUT = "%";
    const LT = "<";
    const GT = ">";
    const EQ = "==";
    const NOT_EQ = "!="; 
    const GT_OR_EQ = ">=";
    const LT_OR_EQ = "<=";
    const LPAREN = "(";
    const RPAREN = ")";
    const TRUEOP = 'TRUE';
    const FALSEOP = 'FALSE';
}