<?php

namespace Ibelousov\MathExec\Tests;

use Ibelousov\MathExec\Lexer\Lexer;
use Ibelousov\MathExec\Lexer\OperatorType;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /** @test */
    public function it_returns_eof_token()
    {
        $lexer = new Lexer('');

        $token = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::EOF);
        $this->assertEquals($token->literal, '');
    }

    /** @test */
    public function it_returns_ident_token()
    {
        $lexer = new Lexer('a');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::IDENT);
        $this->assertEquals($token->literal, 'a');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_number_token()
    {
        $lexer = new Lexer('4');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::NUMBER);
        $this->assertEquals($token->literal, '4');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_plus_token()
    {
        $lexer = new Lexer('+');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::PLUS);
        $this->assertEquals($token->literal, '+');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_minus_token()
    {
        $lexer = new Lexer('-');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::MINUS);
        $this->assertEquals($token->literal, '-');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_asterisk_token()
    {
        $lexer = new Lexer('*');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::ASTERISK);
        $this->assertEquals($token->literal, '*');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_slash_token()
    {
        $lexer = new Lexer('/');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::SLASH);
        $this->assertEquals($token->literal, '/');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_wdiw_token()
    {
        $lexer = new Lexer('//');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::WDIV);
        $this->assertEquals($token->literal, '//');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_roots_token()
    {
        $lexer = new Lexer('\\');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::ROOTS);
        $this->assertEquals($token->literal, '\\');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_modul_token()
    {
        $lexer = new Lexer('%');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::MODUL);
        $this->assertEquals($token->literal, '%');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_power_token()
    {
        $lexer = new Lexer('^');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::POWER);
        $this->assertEquals($token->literal, '^');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_lt_token()
    {
        $lexer = new Lexer('<');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::LT);
        $this->assertEquals($token->literal, '<');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_gt_token()
    {
        $lexer = new Lexer('>');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::GT);
        $this->assertEquals($token->literal, '>');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_eq_token()
    {
        $lexer = new Lexer('==');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::EQ);
        $this->assertEquals($token->literal, '==');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_not_eq_token()
    {
        $lexer = new Lexer('!=');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::NOT_EQ);
        $this->assertEquals($token->literal, '!=');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_gt_or_eq_token()
    {
        $lexer = new Lexer('>=');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::GT_OR_EQ);
        $this->assertEquals($token->literal, '>=');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_lt_or_eq_token()
    {
        $lexer = new Lexer('<=');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::LT_OR_EQ);
        $this->assertEquals($token->literal, '<=');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_lparen_token()
    {
        $lexer = new Lexer('(');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::LPAREN);
        $this->assertEquals($token->literal, '(');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_rparen_token()
    {
        $lexer = new Lexer(')');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::RPAREN);
        $this->assertEquals($token->literal, ')');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_comma_token()
    {
        $lexer = new Lexer(',');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::COMMA);
        $this->assertEquals($token->literal, ',');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_bang_token()
    {
        $lexer = new Lexer('!');

        $token = $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::BANG);
        $this->assertEquals($token->literal, '!');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }

    /** @test */
    public function it_returns_bang_bang_token()
    {
        $lexer = new Lexer('!!');

        $token = $lexer->nextToken();
        $token2= $lexer->nextToken();
        $eof = $lexer->nextToken();

        $this->assertEquals($token->tokenType, OperatorType::BANG);
        $this->assertEquals($token->literal, '!');
        $this->assertEquals($token2->tokenType, OperatorType::BANG);
        $this->assertEquals($token2->literal, '!');
        $this->assertEquals($eof->tokenType, OperatorType::EOF);
        $this->assertEquals($eof->literal, '');
    }
}