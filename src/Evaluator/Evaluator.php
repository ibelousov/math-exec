<?php

namespace Ibelousov\MathExec\Evaluator;

use Ibelousov\MathExec\Exceptions\DivisionByZeroException;
use Ibelousov\MathExec\Exceptions\EvaluatingPowerException;
use Ibelousov\MathExec\Exceptions\IdentifierNotFoundException;
use Ibelousov\MathExec\Exceptions\IncorrectEnvironmentException;
use Ibelousov\MathExec\Exceptions\InvalidNodeException;
use Ibelousov\MathExec\Exceptions\NotAFunctionException;
use Ibelousov\MathExec\Exceptions\TypeMismatchException;
use Ibelousov\MathExec\Exceptions\UnknownOperatorException;
use Ibelousov\MathExec\Exceptions\WrongArgumentNumberException;
use Ibelousov\MathExec\Exceptions\WrongPrefixOperatorException;
use Ibelousov\MathExec\Lexer\Lexer;
use Ibelousov\MathExec\Parser\Parser;
use Ibelousov\MathExec\Ast\NodeInterface;
use Ibelousov\MathExec\Ast\Program;
use Ibelousov\MathExec\Ast\ExpressionStatement;
use Ibelousov\MathExec\Ast\NumberLiteral;
use Ibelousov\MathExec\Ast\Boolean;
use Ibelousov\MathExec\Ast\PrefixExpression;
use Ibelousov\MathExec\Ast\CallExpression;
use Ibelousov\MathExec\Ast\Identifier;
use Ibelousov\MathExec\Ast\InfixExpression;
use Ibelousov\MathExec\Lexer\OperatorType;

class Evaluator
{
    protected $trueObj;
    protected $falseObj;
    protected $builtIns;
    protected $precision;
    protected $program;

    public function __construct(string $input, int $precision = 40)
    {
        $this->trueObj  = new BooleanObj('1');
        $this->falseObj = new BooleanObj('0');
        $this->builtIns = BuiltinCollection::getInstance();
        $this->program  = (new Parser(new Lexer($input)))->parseProgram();
        $this->precision= $precision;
    }

    public static function mathExec($expression, $inner_precision = 40)
    {
        return (new self($expression, $inner_precision))->exec();
    }

    public static function mathPrepare($expression, $inner_precision = 40)
    {
        return new self($expression, $inner_precision);
    }

    public function exec($environment = [], $precision = null)
    {
        $this->environment = $environment;

        if (is_int($precision)) {
            $this->precision = $precision;
        }

        return $this->evalObj($this->program)->value;
    }

    public function evalObj(NodeInterface $node): ObjInterface
    {
        if ($node instanceof Program) {
            return $this->evalProgram($node);
        }

        if ($node instanceof ExpressionStatement) {
            return $this->evalObj($node->expression);
        }

        if ($node instanceof NumberLiteral) {
            return new NumberObj($node->value);
        }

        if ($node instanceof Boolean) {
            return new BooleanObj($node->bool);
        }

        if ($node instanceof PrefixExpression) {
            $right = $this->EvalObj($node->right);
            
            return $this->EvalPrefixExpression($node->operator, $right);
        }

        if ($node instanceof CallExpression) {
            $function = $this->EvalObj($node->function);

            $args = $this->evalExpressions($node->arguments);

            return $this->ApplyFunction($function, $args);
        }

        if ($node instanceof Identifier) {
            return $this->evalIdentifier($node);
        }

        if ($node instanceof InfixExpression) {
            $left = $this->evalObj($node->left);

            $right = $this->evalObj($node->right);

            return $this->evalInfixExpression($node->operator, $left, $right);
        }

        throw new InvalidNodeException();
    }

    public function evalIdentifier($node): ObjInterface
    {
        if (array_key_exists($node->value, $this->environment)) {
            $value = (string)$this->environment[$node->value];

            if (!is_numeric($value)) {
                throw new IncorrectEnvironmentException;
            }

            return new NumberObj($value);
        }

        $builtin = $this->builtIns->getBuiltin($node->value);

        if ($builtin) {
            return $builtin;
        }

        throw new IdentifierNotFoundException($node->value);
    }

    public function applyFunction($function, $args): ObjInterface
    {
        if ($function instanceof BuiltinFunctionObj) {
            $buldinfunction = $function->builtinFunction;

            return $buldinfunction($args);
        }

        throw new NotAFunctionException($function->Type());
    }

    public function evalExpressions($exps): array
    {
        $result = [];

        foreach ($exps as $exp) {
            $result[] = $this->evalObj($exp);
        }

        return $result;
    }

    public function evalProgram($program): ObjInterface
    {
        $result = null;

        foreach ($program->statements as $statement) {
            $result = $this->evalObj($statement);
        }

        return $result;
    }

    public function evalPrefixExpression($operator, $right): ObjInterface
    {
        switch ($operator) {
            case '!':  return $this->evalBangOperatorExpression($right);
            case '-':  return $this->evalMinusPrefixOperatorExpression($right);
            case '\\': return $this->evaluateSqr($right);
        }

        throw new UnknownOperatorException($operator);
    }

    public function evalBangOperatorExpression($right): ObjInterface
    {
        switch ($right->value) {
            case OperatorType::TRUEOP: case null: return $this->falseObj;
            case OperatorType::FALSEOP: default: return $this->trueObj;
        }
    }

    public function evalMinusPrefixOperatorExpression($right): ObjInterface
    {
        if ($right->Type() != ObjType::NUMBER_OBJ) {
            throw new UnknownOperatorException($right->Type());
        }

        return new NumberObj(bcmul($right->value, '-1', $this->precision));
    }

    public function evaluateSqr($operand): ObjInterface
    {
        return new NumberObj(bcsqrt($operand->value, $this->precision));
    }

    public function evalInfixExpression($operator, $left, $right): ObjInterface
    {
        if ($left->Type() == ObjType::NUMBER_OBJ && $right->Type() == ObjType::NUMBER_OBJ) {
            return $this->evalIntegerInfixExpressioin($operator, $left, $right);
        }

        if ($left->Type() != $right->Type()) {
            throw new TypeMismatchException($left->Type() . ' ' . $operator . ' ' . $right->Type());
        }

        if ($operator == '==') {
            return new BooleanObj($left == $right);
        }

        if ($operator == '!=') {
            return new BooleanObj($left != $right);
        }

        throw new UnknownOperatorException($left->Type() . ' ' . $operator . ' ' . $right->Type());
    }

    public function evalIntegerInfixExpressioin($operator, $left, $right): ?ObjInterface
    {
        $leftVal  = $left->value;
        $rightVal = $right->value;

        switch ($operator) {

            case '+':  return new NumberObj($this->evaluateAdd($leftVal, $rightVal));
            case '-':  return new NumberObj($this->evaluateSub($leftVal, $rightVal));
            case '*':  return new NumberObj($this->evaluateMul($leftVal, $rightVal));
            case '^':  return new NumberObj($this->evaluatePow($leftVal, $rightVal));
            case '/':  return new NumberObj($this->evaluateDiv($leftVal, $rightVal));
            case '//': return new NumberObj($this->evaluateWDiv($leftVal, $rightVal));
            case '%':  return new NumberObj($this->evaluateMod($leftVal, $rightVal));

            case '<':  return new BooleanObj($this->evaluateLT($leftVal, $rightVal));
            case '<=': return new BooleanObj($this->evaluateLT($leftVal, $rightVal) || $this->evaluateEQ($leftVal, $rightVal));
            case '>':  return new BooleanObj($this->evaluateGT($leftVal, $rightVal));
            case '>=': return new BooleanObj($this->evaluateGT($leftVal, $rightVal) || $this->evaluateEQ($leftVal, $rightVal));
            case '==': return new BooleanObj($this->evaluateEQ($leftVal, $rightVal));
            case '!=': return new BooleanObj($this->evaluateNEQ($leftVal, $rightVal));

        }

        throw new UnknownOperatorException($left->Type() . ' ' . $operator . ' ' . $right->Type());
    }

    public function evaluateLT($leftVal, $rightVal): int
    {
        return (int)(bccomp($leftVal, $rightVal, $this->precision) == -1);
    }

    public function evaluateGT($leftVal, $rightVal): int
    {
        return (int)(bccomp($leftVal, $rightVal, $this->precision) == 1);
    }

    public function evaluateEQ($leftVal, $rightVal): int
    {
        return (int)(bccomp($leftVal, $rightVal, $this->precision) == 0);
    }

    public function evaluateNEQ($leftVal, $rightVal): int
    {
        return !$this->evaluateEQ($leftVal, $rightVal);
    }

    public function evaluatePow($leftVal, $rightVal)
    {
        if (strpos($leftVal, '.') !== false || strpos($rightVal, '.') !== false) {
            throw new EvaluatingPowerException("Error processing powering: $leftVal and $rightVal should be whole values", 1);
        }

        return bcpow($leftVal, $rightVal, $this->precision);
    }

    public function evaluateDiv($leftVal, $rightVal)
    {
        if (bccomp('0', $rightVal, $this->precision) == 0) {
            throw new DivisionByZeroException("Cannot divide $leftVal by $rightVal");
        }

        return bcdiv($leftVal, $rightVal, $this->precision);
    }

    public function evaluateWDiv($leftVal, $rightVal)
    {
        return bcdiv($leftVal, $rightVal, 0) . '.' . str_repeat('0', $this->precision);
    }

    public function evaluateMul($leftVal, $rightVal)
    {
        return bcmul($leftVal, $rightVal, $this->precision);
    }

    public function evaluateSub($leftVal, $rightVal)
    {
        return bcsub($leftVal, $rightVal, $this->precision);
    }

    public function evaluateAdd($leftVal, $rightVal)
    {
        return bcadd($leftVal, $rightVal, $this->precision);
    }

    public function evaluateMod($leftVal, $rightVal)
    {
        return bcmod($leftVal, $rightVal, $this->precision);
    }
}
