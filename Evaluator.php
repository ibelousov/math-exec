<?php

require_once 'Parser.php';
require_once 'Lexer.php';
require_once 'Ast.php';
require_once 'Obj.php';
require_once 'Environment.php';

$builtins = [
    'floor' => new BuiltinFunctionObj(function(...$args) {
        return new NumberObj(bcsub($args[0][0]->value, '0.5', 0));
    }),
    'ceil' => new BuiltinFunctionObj(function(...$args) {
        return new NumberObj(bcadd($args[0][0]->value, '0.5', 0));
    }),
    'format' => new BuiltinFunctionObj(function(...$args) {
        return new NumberObj(bcmul($args[0][0]->value, 1, $args[0][1]->value));
    })
];

$BULL_OBJ_TRUE = new BooleanObj(true);
$BULL_OBJ_FALSE= new BooleanObj(false);
$NULL_OBJ = new NIL();

class Evaluator
{
    public static function EvalObj(Node $node, ?Environment $env = null): ?Obj
    {
        if($node instanceof Program)
            return self::EvalProgram($node,$env);

        if($node instanceof ExpressionStatement)
            return self::EvalObj($node->expression, $env);

        if($node instanceof NumberLiteral)
            return new NumberObj($node->value);

        if($node instanceof Boolean) 
            return new BooleanObj($node->bool);

        if($node instanceof PrefixExpression) {
            $right = self::EvalObj($node->right);
            
            if(self::isError($right)) 
                return $right;
            
            return self::EvalPrefixExpression($node->operator, $right);
        }

        if($node instanceof CallExpression) {

            $function = self::EvalObj($node->function, $env);

            if(self::isError($function)) return $function;

            $args = self::EvalExpressions($node->arguments, $env);
            
            if(count($args) == 1 && self::isError($args[0])) return $args[0];

            return self::ApplyFunction($function, $args);
        }

        if($node instanceof Identifier)
            return self::EvalIdentifier($node, $env);

        if($node instanceof InfixExpression) {
            $left = self::EvalObj($node->left, $env);
            if(self::isError($left)) return $left;
            
            $right = self::EvalObj($node->right, $env);
            if(self::isError($right)) return $right;

            return self::EvalInfixExpression($node->operator, $left, $right, $env);
        }

        return null;
    }

    public static function EvalIdentifier($node, $env): ?Obj
    {

        $builtins = $GLOBALS['builtins'];

        if(array_key_exists($node->value, $builtins))
            return $builtins[$node->value];

        return self::newError("identifier not found: " . $node->value);
    }

    public static function ApplyFunction($function, $args): ?Obj
    {
        if($function instanceof BuiltinFunctionObj) {

            $buldinfunction = $function->builtinFunction;

            return $buldinfunction($args);
        }

        return new ErrorObj("not a function: ", $function->Type());
    }

    public static function EvalExpressions($exps, $env): array
    {
        $result = [];

        foreach($exps as $exp) {
            $evaluated = self::EvalObj($exp, $env);

            if(self::isError($evaluated)) {
                return new Obj($evaluated);
            }

            $result[] = $evaluated;
        }

        return $result;
    }

    public static function EvalProgram($program): ?Obj
    {
        $result = null;

        foreach($program->statements as $statement) {
            $result = self::EvalObj($statement);

            if($result instanceof ReturnValue)
                return $result->returnValue->value;

            if($result instanceof ErrorObj)
                return $result;
        }

        return $result;
    }

    public static function EvalPrefixExpression($operator, $right): ?Obj
    {
        switch ($operator) {
            case '!':  return self::EvalBangOperatorExpression($right);
            case '-':  return self::EvalMinusPrefixOperatorExpression($right);
            case '\\': return self::EvaluateSqr($right);
            default:   return self::newError("unknown operator: %s%s", $operator, $right->Type());
        }
    }

    public static function EvalBangOperatorExpression($right): ?Obj
    {
        switch ($right->value) {
            case TRUEOP: return new BooleanObj(false);
            case FALSEOP: return new BooleanObj(true);
            case NULL: return new BooleanObj(true);
            default: return new BooleanObj(false);
        }
    }

    public static function EvalMinusPrefixOperatorExpression($right): ?Obj
    {
        if($right->Type() != INTEGER_OBJ)
            return self::newError("unknown operator: -", $right->Type());

        $value = $right->value;
        return new Numberbj(-$value);
    }

    public static function EvaluateSqr($operand): ?Obj
    {
        return new NumberObj(bcsqrt($operand->value, 100));
    }

    public static function EvalInfixExpression($operator, $left, $right): ?Obj
    {
        if($left->Type() == NUMBER_OBJ && $right->Type() == NUMBER_OBJ)
            return self::EvalIntegerInfixExpressioin($operator, $left, $right);

        if($left->Type() != $right->Type())
            return self::newError("type mismatch: ", $left->Type(), $operator, $right->Type());

        if($left->Type() == STRING_OBJ && $right->Type() == STRING_OBJ)
            return self::EvalStringInfixExpression($operator, $left, $right);

        if($operator == '==')
            return new BooleanObj($left == $right);

        if($operator == '!=')
            return new BooleanObj($left != $right);

        return self::newError("unknown operator: ", $left->Type(), $operator, $right->Type());
    }

    public static function EvalIntegerInfixExpressioin($operator, $left, $right): ?Obj
    {
        $leftVal = $left->value;
        $rightVal = $right->value;

        switch ($operator) {
            case '+':  return new NumberObj(self::EvaluateAdd($leftVal, $rightVal));
            case '-':  return new NumberObj(self::EvaluateSub($leftVal, $rightVal));
            case '*':  return new NumberObj(self::EvaluateMul($leftVal, $rightVal));
            case '^':  return new NumberObj(self::EvaluatePow($leftVal, $rightVal));
            case '/':  return new NumberObj(self::EvaluateDiv($leftVal, $rightVal));
            case '//': return new NumberObj(self::EvaluateWDiv($leftVal, $rightVal));
            case '%':  return new NumberObj(self::EvaluateMod($leftVal, $rightVal));
            case '<':  return new BooleanObj(self::EvaluateLT($leftVal, $rightVal));
            case '<=': return new BooleanObj(self::EvaluateLT($leftVal, $rightVal) || self::EvaluateEQ($leftVal, $rightVal));
            case '>':  return new BooleanObj(self::EvaluateGT($leftVal, $rightVal));
            case '>=': return new BooleanObj(self::EvaluateGT($leftVal, $rightVal) || self::EvaluateEQ($leftVal, $rightVal));
            case '==': return new BooleanObj(self::EvaluateEQ($leftVal, $rightVal));
            case '!=': return new BooleanObj(self::EvaluateNEQ($leftVal, $rightVal));
            default: return self::newError("unknown operator: ", $left->Type(), $operator, $right->Type());
        }
    }

    public static function EvaluateLT($leftVal, $rightVal)
    {
        return bccomp($leftVal, $rightVal, 100) == -1;
    }

    public static function EvaluateGT($leftVal, $rightVal)
    {
        return bccomp($leftVal, $rightVal, 100) == 1;
    }

    public static function EvaluateEQ($leftVal, $rightVal)
    {
        return bccomp($leftVal, $rightVal, 100) == 0;
    }

    public static function EvaluatePow($leftVal, $rightVal)
    {
        if(strpos($leftVal, '.') !== false || strpos($rightVal, '.') !== false)
            throw new Exception("Error processing powering: $leftVal, $rightVal. Should be whole values", 1);
            

        return bcpow($leftVal, $rightVal, 100);
    }

    public static function EvaluateDiv($leftVal, $rightVal)
    {
        return bcdiv($leftVal, $rightVal, 100);
    }

    public static function EvaluateWDiv($leftVal, $rightVal)
    {
        return bcdiv($leftVal, $rightVal, 0) . '.' . str_repeat('0', 100);
    }

    public static function EvaluateMul($leftVal, $rightVal)
    {
        return bcmul($leftVal, $rightVal, 100);
    }

    public static function EvaluateSub($leftVal, $rightVal)
    {
        return bcsub($leftVal, $rightVal, 100);
    }

    public static function EvaluateAdd($leftVal, $rightVal)
    {
        return bcadd($leftVal, $rightVal, 100);
    }

    public static function EvaluateMod($leftVal, $rightVal)
    {
        return bcmod($leftVal, $rightVal, 100);
    }

    public static function isError($obj): bool 
    {
        if($obj != null)
            return $obj->Type() == ERROR_OBJ;

        return false;
    }

    public static function newError($string, ...$a): ErrorObj
    {
        if($a) $string .= implode(" ", $a);

        return new ErrorObj($string);
    }
}


$evaluated = Evaluator::EvalObj((new Parser(new Lexer('format(2.55345453, 3)')))->parseProgram());

var_dump($evaluated);