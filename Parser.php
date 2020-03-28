<?php

require_once "Ast.php";
require_once "Lexer.php";
require_once "Token.php";

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
    EQ          => EQUALS,
    NOT_EQ      => EQUALS,
    LT          => LESSGT,
    GT          => LESSGT,
    LT_OR_EQ    => LESSGT,
    GT_OR_EQ    => LESSGT,
    PLUS        => SUM,
    MINUS       => SUM,
    MODUL       => MOD,
    SLASH       => PRODUCT,
    ASTERISK    => PRODUCT,
    WDIV        => PRODUCT,
    POWER       => POWERP,
    LPAREN      => CALL
];

interface prefixParseFn
{
    public function prefixParseFn(): ?Expression;
}

interface infixParseFn
{
    public function infixParseFn(Expression $exp): ?Expression;
}

class Parser
{
    public $lexer;
    public $errors;

    public $curToken;
    public $peekToken;

    public $prefixParseFns;
    public $infixParseFns;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->errors= [];

        $this->prefixParseFns = [];
        $this->infixParseFns = [];

        $this->registerPrefix(IDENT, function(){
            return $this->parseIdentifier();
        });
        $this->registerPrefix(NUMBER, function(){
            return $this->parseNumberLiteral();
        });
        $this->registerPrefix(BANG, function(){
            return $this->parsePrefixExpression();
        });
        $this->registerPrefix(MINUS, function(){
            return $this->parsePrefixExpression();
        });
        $this->registerPrefix(ROOTS, function() {
            return $this->parsePrefixExpression();
        });

        $this->registerPrefix(LPAREN, function(){
            return $this->parseGroupedExpression();
        });
        $this->registerInfix(PLUS, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(MINUS, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(SLASH, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(WDIV, function($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(ASTERISK, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(POWER, function($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(EQ, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(NOT_EQ, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(LT, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(LT_OR_EQ, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(GT, function($expression){
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(GT_OR_EQ, function($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(MODUL, function($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(LPAREN, function($expression) {
            return $this->parseCallExpression($expression);
        });
        
        $this->nextToken();
        $this->nextToken();
    }

    public function parseProgram(): Program
    {
        $program = new Program();
        $program->statements = [];

        while($this->curToken->tokenType != EOF) {
            $stmt = $this->parseStatement();
            
            if($stmt != null) {
                $program->statements[] = $stmt;
            }
            $this->nextToken();
        }

        return $program;
    }

    public function parseStatement(): ?Statement
    {
        return $this->parseExpressionStatement();
    }

    public function parseExpressionStatement(): ?ExpressionStatement
    {
        $stmt = new ExpressionStatement($this->curToken);

        $stmt->expression = $this->parseExpression(LOWEST);

        return $stmt;
    }

    public function parseExpression($precedence): ?Expression
    {
        $prefix = array_key_exists($this->curToken->tokenType, $this->prefixParseFns);

        if(!$prefix) {
            $this->noPrefixParseFnError($this->curToken->tokenType);

            return null;
        }

        $leftExp = $this->prefixParseFns[$this->curToken->tokenType]();

        while(!$this->peekTokenIs(EOF) && $precedence < $this->peekPrecedence())
        {
            $infix = array_key_exists($this->peekToken->tokenType, 
                $this->infixParseFns);

            if(!$infix) {
                return $leftExp;
            }

            $infix = $this->infixParseFns[$this->peekToken->tokenType];

            $this->nextToken();

            $leftExp = $infix($leftExp);

        }

        return $leftExp;
    }

    public function parsePrefixExpression(): ?Expression
    {
        $expression = new PrefixExpression($this->curToken, 
            $this->curToken->literal);

        $this->nextToken();

        $expression->right = $this->parseExpression(PREFIX);

        return $expression;
    }

    public function parseInfixExpression(Expression $left): Expression
    {
        $expression = new InfixExpression(
            $this->curToken, $this->curToken->literal, $left
        );

        $precedence = $this->curPrecedence();
        
        $this->nextToken();

        $expression->right = $this->parseExpression($precedence);

        return $expression;
    }

    public function parseExpressionList($end): ?array
    {
        $list = [];

        if($this->peekTokenIs($end)) {
            $this->nextToken();

            return $list;
        }

        $this->nextToken();

        $list[] = $this->parseExpression(LOWEST);

        while($this->peekTokenIs(COMMA)) {
            $this->nextToken();
            $this->nextToken();
            $list[] = $this->parseExpression(LOWEST);
        }

        if(!$this->expectPeek($end)) {
            return null;
        }

        return $list;
    }

    public function parseFunctionLiteral(): ?Expression
    {
        $lit = new FunctionLiteral($this->curToken);

        if(!$this->expectPeek(LPAREN))
            return null;

        $lit->parameters = $this->parseFunctionParameters();

        if(!$this->expectPeek(LBRACE))
            return null;

        $lit->body = $this->parseBlockStatement();

        return $lit;
    }

    public function parseIdentifier(): ?Expression
    {
        return new Identifier($this->curToken, $this->curToken->literal);
    }

    public function parseCallExpression($function): Expression
    {
        $exp = new CallExpression($this->curToken, $function);


        $exp->arguments = $this->parseExpressionList(RPAREN);

        return $exp;
    }

    public function parseCallArguments()
    {
        $args = [];

        if($this->peekTokenIs(RPAREN)) {
            $this->nextToken();

            return $args;
        }

        $this->nextToken();
        $args[] = $this->parseExpression(LOWEST);

        while($this->peekTokenIs(COMMA)) {
            $this->nextToken();
            $this->nextToken();

            $args[] = $this->parseExpression(LOWEST);
        }

        if(!$this->expectPeek(RPAREN))
            return null;

        return $args;
    }

    public function parseNumberLiteral(): ?Expression
    {
        $lit = new NumberLiteral($this->curToken);

        if(!is_numeric($this->curToken->literal)) {
            $this->errors[] = "could not parse {$lit->value} as integer";

            return null;
        }

        $lit->value = $this->curToken->literal;

        return $lit;
    }

    public function parseGroupedExpression(): ?Expression
    {
        $this->nextToken();

        $exp = $this->parseExpression(LOWEST);

        if(!$this->expectPeek(RPAREN)) {
            return null;
        }

        return $exp;
    }

    public function parseBoolean(): Expression 
    {
        return new Boolean($this->curToken, $this->curTokenIs(TRUEOP));
    }

    public function curTokenIs($t): bool
    {
        return $this->curToken->tokenType == $t;
    }

    public function peekTokenIs($t): bool
    {
        return $this->peekToken->tokenType == $t;
    }

    public function peekPrecedence(): int
    {
        if(array_key_exists($this->peekToken->tokenType, precedences))
            return precedences[$this->peekToken->tokenType];

        return LOWEST;
    }

    public function curPrecedence(): int
    {
        if(array_key_exists($this->curToken->tokenType, precedences)) {
            return precedences[$this->curToken->tokenType];
        }

        return LOWEST;
    }

    public function expectPeek($t): bool
    {
        if($this->peekTokenIs($t)) {
            $this->nextToken();
            return true;
        }
        
        $this->peekError($t);

        return false;
    }

    public function registerPrefix($tokenType, $fn)
    {
        $this->prefixParseFns[$tokenType] = $fn;
    }

    public function registerInfix($tokenType, $fn)
    {
        $this->infixParseFns[$tokenType] = $fn;
    }

    public function noPrefixParseFnError($token)
    {
        $this->errors[] = "no prefix parse function for {$token} found";
    }

    public function Errors()
    {
        return $this->errors;
    }

    public function peekError($tokenType)
    {
        $exp = $tokenType;
        $got = $this->peekToken->tokenType;

        $this->errors[] = "expected next token to be $exp, got $got instead";
    }

    public function nextToken()
    {
        $this->curToken = $this->peekToken;
        $this->peekToken = $this->lexer->nextToken();
    }
}