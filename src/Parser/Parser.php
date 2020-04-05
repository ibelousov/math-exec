<?php

namespace Ibelousov\MathExec\Parser;

use Ibelousov\MathExec\Exceptions\WrongPrefixOperatorException;
use Ibelousov\MathExec\Lexer\Lexer;
use Ibelousov\MathExec\Lexer\OperatorType;
use Ibelousov\MathExec\Ast\AbstractExpression;
use Ibelousov\MathExec\Ast\Boolean;
use Ibelousov\MathExec\Ast\CallExpression;
use Ibelousov\MathExec\Ast\ExpressionStatement;
use Ibelousov\MathExec\Ast\Identifier;
use Ibelousov\MathExec\Ast\InfixExpression;
use Ibelousov\MathExec\Ast\NumberLiteral;
use Ibelousov\MathExec\Ast\PrefixExpression;
use Ibelousov\MathExec\Ast\Program;

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

        $this->registerPrefix(OperatorType::IDENT, function () {
            return $this->parseIdentifier();
        });
        $this->registerPrefix(OperatorType::NUMBER, function () {
            return $this->parseNumberLiteral();
        });
        $this->registerPrefix(OperatorType::BANG, function () {
            return $this->parsePrefixExpression();
        });
        $this->registerPrefix(OperatorType::MINUS, function () {
            return $this->parsePrefixExpression();
        });
        $this->registerPrefix(OperatorType::ROOTS, function () {
            return $this->parsePrefixExpression();
        });

        $this->registerPrefix(OperatorType::LPAREN, function () {
            return $this->parseGroupedExpression();
        });
        $this->registerInfix(OperatorType::PLUS, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::MINUS, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::SLASH, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::WDIV, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::ASTERISK, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::POWER, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::EQ, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::NOT_EQ, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::LT, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::LT_OR_EQ, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::GT, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::GT_OR_EQ, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::MODUL, function ($expression) {
            return $this->parseInfixExpression($expression);
        });
        $this->registerInfix(OperatorType::LPAREN, function ($expression) {
            return $this->parseCallExpression($expression);
        });
        
        $this->nextToken();
        $this->nextToken();
    }

    public function parseProgram(): Program
    {
        $program = new Program();
        $program->statements = [];

        while ($this->curToken->tokenType != OperatorType::EOF) {
            $stmt = $this->parseStatement();
            
            if ($stmt != null) {
                $program->statements[] = $stmt;
            }
            $this->nextToken();
        }

        return $program;
    }

    public function parseStatement(): ExpressionStatement
    {
        return $this->parseExpressionStatement();
    }

    public function parseExpressionStatement(): ExpressionStatement
    {
        $stmt = new ExpressionStatement($this->curToken);

        $stmt->expression = $this->parseExpression(Precedence::LOWEST);

        return $stmt;
    }

    public function parseExpression($precedence): AbstractExpression
    {
        $prefix = array_key_exists($this->curToken->tokenType, $this->prefixParseFns);

        if (!$prefix) {
            throw new WrongPrefixOperatorException($this->curToken->tokenType);
        }

        $leftExp = $this->prefixParseFns[$this->curToken->tokenType]();

        while (!$this->peekTokenIs(OperatorType::EOF) && $precedence < $this->peekPrecedence()) {
            $infix = array_key_exists($this->peekToken->tokenType,
                $this->infixParseFns);

            if (!$infix) {
                return $leftExp;
            }

            $infix = $this->infixParseFns[$this->peekToken->tokenType];

            $this->nextToken();

            $leftExp = $infix($leftExp);
        }

        return $leftExp;
    }

    public function parsePrefixExpression(): AbstractExpression
    {
        $expression = new PrefixExpression($this->curToken,
            $this->curToken->literal);

        $this->nextToken();

        $expression->right = $this->parseExpression(Precedence::PREFIX);

        return $expression;
    }

    public function parseInfixExpression(AbstractExpression $left): AbstractExpression
    {
        $expression = new InfixExpression($this->curToken, $this->curToken->literal, $left);

        $precedence = $this->curPrecedence();
        
        $this->nextToken();

        $expression->right = $this->parseExpression($precedence);

        return $expression;
    }

    public function parseExpressionList($end): ?array
    {
        $list = [];

        if ($this->peekTokenIs($end)) {
            $this->nextToken();

            return $list;
        }

        $this->nextToken();

        $list[] = $this->parseExpression(Precedence::LOWEST);

        while ($this->peekTokenIs(OperatorType::COMMA)) {
            $this->nextToken();
            $this->nextToken();
            $list[] = $this->parseExpression(Precedence::LOWEST);
        }

        if (!$this->expectPeek($end)) {
            return null;
        }

        return $list;
    }

    public function parseIdentifier(): AbstractExpression
    {
        return new Identifier($this->curToken, $this->curToken->literal);
    }

    public function parseCallExpression($function): AbstractExpression
    {
        $exp = new CallExpression($this->curToken, $function);

        $exp->arguments = $this->parseExpressionList(OperatorType::RPAREN);

        return $exp;
    }

    public function parseNumberLiteral(): AbstractExpression
    {
        $lit = new NumberLiteral($this->curToken);

        if (!is_numeric($this->curToken->literal)) {
            $this->errors[] = "could not parse {$lit->value} as integer";

            return null;
        }

        $lit->value = $this->curToken->literal;

        return $lit;
    }

    public function parseGroupedExpression(): AbstractExpression
    {
        $this->nextToken();

        $exp = $this->parseExpression(Precedence::LOWEST);

        if (!$this->expectPeek(OperatorType::RPAREN)) {
            return null;
        }

        return $exp;
    }

    public function parseBoolean(): AbstractExpression
    {
        return new Boolean($this->curToken, $this->curTokenIs(OperatorType::TRUEOP));
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
        if (array_key_exists($this->peekToken->tokenType, Precedence::precedences)) {
            return Precedence::precedences[$this->peekToken->tokenType];
        }

        return Precedence::LOWEST;
    }

    public function curPrecedence(): int
    {
        if (array_key_exists($this->curToken->tokenType, Precedence::precedences)) {
            return Precedence::precedences[$this->curToken->tokenType];
        }

        return Precedence::LOWEST;
    }

    public function expectPeek($t): bool
    {
        if ($this->peekTokenIs($t)) {
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
