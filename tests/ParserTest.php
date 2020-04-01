<?php


namespace Ibelousov\MathExec\Tests;


use Ibelousov\MathExec\Parser\Parser;
use Ibelousov\MathExec\Lexer\Lexer;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function parse_plus()
    {
        $lexer = new Lexer("4 + 4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(4 + 4)");
    }

    /** @test */
    public function parse_minus()
    {
        $lexer = new Lexer("4 - 4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(4 - 4)");
    }

    /** @test */
    public function parse_multiply()
    {
        $lexer = new Lexer("4 * 4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(4 * 4)");
    }

    /** @test */
    public function parse_div()
    {
        $lexer = new Lexer("4 / 4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(4 / 4)");
    }

    /** @test */
    public function parse_modul()
    {
        $lexer = new Lexer("4 % 4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(4 % 4)");
    }

    /** @test */
    public function parse_wdiw()
    {
        $lexer = new Lexer("4 // 4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(4 // 4)");
    }

    /** @test */
    public function parse_roots()
    {
        $lexer = new Lexer("\\4");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(\\4)");
    }

    /** @test */
    public function parse_power()
    {
        $lexer = new Lexer("2^2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 ^ 2)");
    }

    /** @test */
    public function parse_lt()
    {
        $lexer = new Lexer("2<2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 < 2)");
    }

    /** @test */
    public function parse_gt()
    {
        $lexer = new Lexer("2>2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 > 2)");
    }

    /** @test */
    public function parse_eq()
    {
        $lexer = new Lexer("2==2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 == 2)");
    }

    /** @test */
    public function parse_not_eq()
    {
        $lexer = new Lexer("2!=2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 != 2)");
    }

    /** @test */
    public function parse_gt_or_eq()
    {
        $lexer = new Lexer("2>=2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 >= 2)");
    }

    /** @test */
    public function parse_lt_or_eq()
    {
        $lexer = new Lexer("2<=2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(2 <= 2)");
    }

    /** @test */
    public function parse_paren()
    {
        $lexer = new Lexer("(2)");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "2");
    }

    /** @test */
    public function parse_comma()
    {
        $lexer = new Lexer("abs(2,2)");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "abs(2, 2)");
    }

    /** @test */
    public function parse_bang()
    {
        $lexer = new Lexer("!2");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(!2)");
    }

    /** @test */
    public function parse_trueop()
    {
        $lexer = new Lexer("!true");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(!true)");
    }

    /** @test */
    public function parse_falseop()
    {
        $lexer = new Lexer("!false");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "(!false)");
    }

    /** @test */
    public function parse_associativity()
    {
        $lexer = new Lexer("2 + 2 * 2 / 2 ^ 2 == 2 / 2 * 2 + 2 / 2 ^ 2 * (2 % 2 + 2) / (!2)");
        $parser= new Parser($lexer);

        $program = $parser->parseProgram();

        $this->assertEquals($program->String(), "((2 + ((2 * 2) / (2 ^ 2))) == (((2 / 2) * 2) + (((2 / (2 ^ 2)) * ((2 % 2) + 2)) / (!2))))");
    }
}