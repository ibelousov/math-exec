<?php

namespace Ibelousov\MathExec\Evaluator;

use Ibelousov\MathExec\Exceptions\IdentifierNotFoundException;
use Ibelousov\MathExec\Exceptions\NotAFunctionException;
use Ibelousov\MathExec\Exceptions\TypeMismatchException;
use Ibelousov\MathExec\Exceptions\UnknownOperatorException;
use Ibelousov\MathExec\Exceptions\WrongArgumentNumberException;
use Ibelousov\MathExec\Exceptions\WrongPrefixOperatorException;
use Ibelousov\MathExec\Parser\Parser;
use Ibelousov\MathExec\Ast\{NodeInterface,
    Program,
    ExpressionStatement,
    NumberLiteral,
    Boolean,
    PrefixExpression,
    CallExpression,
    Identifier,
    InfixExpression};
use Ibelousov\MathExec\Lexer\OperatorType;

class Evaluator
{
    protected $trueObj;
    protected $falseObj;
    protected $nullObj;
    protected $builtIns;
    protected $precision;

    public function __construct(Parser $parser, int $precision)
    {
        $this->trueObj = new BooleanObj(true);
        $this->falseObj= new BooleanObj(false);
        $this->builtIns= BuiltinCollection::getInstance();
        $this->parser  = $parser;
        $this->precision = $precision;
    }

    public function exec()
    {
        return $this->evalObj($this->parser->parseProgram());
    }

    public function evalObj(NodeInterface $node): ObjInterface
    {
        if($node instanceof Program)
            return $this->EvalProgram($node);

        if($node instanceof ExpressionStatement)
            return $this->EvalObj($node->expression);

        if($node instanceof NumberLiteral)
            return new NumberObj($node->value);

        if($node instanceof Boolean) 
            return new BooleanObj($node->bool);

        if($node instanceof PrefixExpression) {
            $right = $this->EvalObj($node->right);
            
            if(self::isError($right)) 
                return $right;
            
            return $this->EvalPrefixExpression($node->operator, $right);
        }

        if($node instanceof CallExpression) {

            $function = $this->EvalObj($node->function);

            if($this->isError($function)) return $function;

            $args = $this->evalExpressions($node->arguments);
            
            if(count($args) == 1 && $this->isError($args[0])) {
                return $args[0];
            }

            return $this->ApplyFunction($function, $args);
        }

        if($node instanceof Identifier)
            return $this->evalIdentifier($node);

        if($node instanceof InfixExpression) {
            $left = $this->evalObj($node->left);
            if($this->isError($left)) {
                return $left;
            }
            
            $right = $this->evalObj($node->right);
            if($this->isError($right)) {
                return $right;
            }

            return $this->evalInfixExpression($node->operator, $left, $right);
        }

        return null;
    }

    public function evalIdentifier($node): ?ObjInterface
    {
        $builtin = $this->builtIns->getBuiltin($node->value);

        if($builtin)
            return $builtin;

        throw new IdentifierNotFoundException($node->value);
    }

    public function applyFunction($function, $args): ?ObjInterface
    {
        if($function instanceof BuiltinFunctionObj) {

            $buldinfunction = $function->builtinFunction;

            return $buldinfunction($args);
        }

        throw new NotAFunctionException($function->Type());
    }

    public function evalExpressions($exps): array
    {
        $result = [];

        foreach($exps as $exp) {
            $evaluated = $this->evalObj($exp);

            if($this->isError($evaluated)) {
                throw new WrongArgumentNumberException();
            }

            $result[] = $evaluated;
        }

        return $result;
    }

    public function evalProgram($program): ?ObjInterface
    {
        $result = null;

        foreach($program->statements as $statement) {
            $result = $this->evalObj($statement);
        }

        return $result;
    }

    public function evalPrefixExpression($operator, $right): ?ObjInterface
    {
        switch ($operator) {
            case '!':  return $this->evalBangOperatorExpression($right);
            case '-':  return $this->evalMinusPrefixOperatorExpression($right);
            case '\\': return $this->evaluateSqr($right);
        }

        throw new UnknownOperatorException();
    }

    public function evalBangOperatorExpression($right): ObjInterface
    {
        switch ($right->value) {
            case OperatorType::TRUEOP: case NULL: return $this->falseObj;
            case OperatorType::FALSEOP: default: return $this->trueObj;
        }
    }

    public function evalMinusPrefixOperatorExpression($right): ObjInterface
    {
        if($right->Type() != ObjType::INTEGER_OBJ)
            throw new UnknownOperatorException($right->Type());

        return new NumberObj(bcmul($right->value, '-1', $this->precision));
    }

    public function evaluateSqr($operand): ?ObjInterface
    {
        return new NumberObj(bcsqrt($operand->value, $this->precision));
    }

    public function evalInfixExpression($operator, $left, $right): ?ObjInterface
    {
        if($left->Type() == ObjType::NUMBER_OBJ && $right->Type() == ObjType::NUMBER_OBJ)
            return $this->evalIntegerInfixExpressioin($operator, $left, $right);

        if($left->Type() != $right->Type())
            throw new TypeMismatchException($left->Type() . ' ' . $operator . ' ' . $right->Type());

        if($operator == '==')
            return new BooleanObj($left == $right);

        if($operator == '!=')
            return new BooleanObj($left != $right);

        throw new UnknownOperatorException($left->Type() . ' ' . $operator . ' ' . $right->Type());
    }

    public function evalIntegerInfixExpressioin($operator, $left, $right): ?ObjInterface
    {
        $leftVal = $left->value;
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

    public function evaluateLT($leftVal, $rightVal)
    {
        return bccomp($leftVal, $rightVal, $this->precision) == -1;
    }

    public function evaluateGT($leftVal, $rightVal)
    {
        return bccomp($leftVal, $rightVal, $this->precision) == 1;
    }

    public function evaluateEQ($leftVal, $rightVal)
    {
        return bccomp($leftVal, $rightVal, $this->precision) == 0;
    }

    public function evaluateNEQ($leftVal, $rightVal)
    {
        return !$this->evaluateEQ($leftVal, $rightVal);
    }

    public function evaluatePow($leftVal, $rightVal)
    {
        if(strpos($leftVal, '.') !== false || strpos($rightVal, '.') !== false)
            throw new Exception("Error processing powering: $leftVal and $rightVal should be whole values", 1);

        return bcpow($leftVal, $rightVal, $this->precision);
    }

    public function evaluateDiv($leftVal, $rightVal)
    {
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

    public function isError($obj): bool
    {
        if($obj != null)
            return $obj->Type() == ObjType::ERROR_OBJ;

        return false;
    }

    public function newError($string, ...$a): ErrorObj
    {
        if($a) $string .= implode(" ", $a);

        return new ErrorObj($string);
    }
}