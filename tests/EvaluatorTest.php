<?php


namespace Ibelousov\MathExec\Tests;


use Ibelousov\MathExec\Evaluator\Evaluator;
use Ibelousov\MathExec\Exceptions\EvaluatingPowerException;
use Ibelousov\MathExec\Exceptions\IdentifierNotFoundException;
use Ibelousov\MathExec\Exceptions\UnknownNumberFormatException;
use Ibelousov\MathExec\Exceptions\WrongPrefixOperatorException;
use Ibelousov\MathExec\Lexer\Lexer;
use Ibelousov\MathExec\Parser\Parser;
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase
{
    /** @test */
    public function evaluate_associativity()
    {
        $executor = new Evaluator("2 + 2 * 2", 0);

        $this->assertEquals((string)($executor->exec()), '6');
    }

    /** @test */
    public function evaluate_multiplication()
    {
        $executor = new Evaluator("2 * 2", 0);

        $this->assertEquals((string)($executor->exec()), '4');
    }

    /** @test */
    public function evaluate_division()
    {
        $executor = new Evaluator("10 / 2", 0);

        $this->assertEquals((string)($executor->exec()), '5');
    }

    /** @test */
    public function evaluate_power()
    {
        $executor = new Evaluator("2 ^ 32", 0);

        $this->assertEquals((string)($executor->exec()), '4294967296');
    }

    /** @test */
    public function evaluate_wdiv()
    {
        $executor = new Evaluator("32.54 // 32", 10);

        $this->assertEquals((string)($executor->exec()), '1');
    }

    /** @test */
    public function evaluate_roots_whole()
    {
        $executor = new Evaluator("\\25", 0);

        $this->assertEquals((string)($executor->exec()), '5');
    }

    /** @test */
    public function evaluate_roots_prec()
    {
        $executor = new Evaluator("\\2", 44);

        $const = '1.41421356237309504880168872420969807856967187';
        // Begin and end, cause i cannot properly compare strings with numbers
        $this->assertEquals("BEGIN{$const}END", "BEGIN" . $executor->exec() . "END");
    }

    /** @test */
    public function evaluate_modul()
    {
        $executor = new Evaluator("34 % 13", 0);

        $this->assertEquals('8', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_lt()
    {
        $executor = new Evaluator("34 / 34.5 < 1", 0);

        $this->assertEquals('1', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_gt()
    {
        $executor = new Evaluator("34 / 33 > 1", 2);

        $this->assertEquals('1', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_eq()
    {
        $executor = new Evaluator("34 / 33 == 68 / 66", 2);

        $this->assertEquals('1', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_noteq()
    {
        $executor = new Evaluator("34 / 33 != 66 / 66", 2);

        $this->assertEquals('1', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_gt_or_eq()
    {
        $executor = new Evaluator("33 >= 33", 2);

        $this->assertEquals('1', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_lt_or_eq()
    {
        $executor = new Evaluator("33 <= 33", 2);

        $this->assertEquals('1', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_parenthesis()
    {
        $executor = new Evaluator("(2+2) * 2", 0);

        $this->assertEquals('8', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_function()
    {
        $executor = new Evaluator("floor(2+2) * 3", 1);

        $this->assertEquals('12.0', (string)($executor->exec()));
    }

    /** @test */
    public function evaluate_power_exception()
    {
        $this->expectException(EvaluatingPowerException::class);

        (new Evaluator("2^3.4", 5))->exec();
    }

    /** @test */
    public function evaluate_undefined_identified_exception()
    {
        $this->expectException(IdentifierNotFoundException::class);

        (new Evaluator('identifier(2)'))->exec();
    }

    /** @test */
    public function evaluate_prefix_expression_exception()
    {
        $this->expectException(WrongPrefixOperatorException::class);

        (new Evaluator('+2'))->exec();
    }

    /** @test */
    public function evaluate_builtin_floor_positive_functions()
    {
        $given = (new Evaluator('floor(4.5)'))->exec();

        $this->assertEquals('4', $given);
    }

    /** @test */
    public function evaluate_builtin_floor_negative_functions()
    {
        $given = (new Evaluator('floor(-4.5)'))->exec();

        $this->assertEquals('-5', $given);
    }

    /** @test */
    public function evaluate_builtin_floor_whole_positive_functions()
    {
        $given = (new Evaluator('floor(4)'))->exec();

        $this->assertEquals('4', $given);
    }

    /** @test */
    public function evaluate_builtin_floor_whole_negative_functions()
    {
        $given = (new Evaluator('floor(-4)'))->exec();

        $this->assertEquals('-4', $given);
    }

    /** @test */
    public function evaluate_builtin_ceil_positive_functions()
    {
        $given = (new Evaluator('ceil(4.5)'))->exec();

        $this->assertEquals('5', $given);
    }

    /** @test */
    public function evaluate_builtin_ceil_negative_functions()
    {
        $given = (new Evaluator('ceil(-4.5)'))->exec();

        $this->assertEquals('-4', $given);
    }

    /** @test */
    public function evaluate_builtin_ceil_whole_positive_functions()
    {
        $given = (new Evaluator('ceil(4)'))->exec();

        $this->assertEquals('4', $given);
    }

    /** @test */
    public function evaluate_builtin_ceil_whole_negative_functions()
    {
        $given = (new Evaluator('ceil(-0.5)'))->exec();

        $this->assertEquals('0', $given);
    }

    /** @test */
    public function evaluate_combinations_of_functions()
    {
        $given = (new Evaluator('format(format(ceil(4.3), 2) * format(floor(4.5), 2), 2)'))->exec();

        $this->assertEquals('BEGIN20.00END', "BEGIN{$given}END");
    }
}