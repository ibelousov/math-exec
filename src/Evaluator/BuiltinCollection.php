<?php


namespace Ibelousov\MathExec\Evaluator;


use Ibelousov\MathExec\Exceptions\BuiltinFunctionExistException;
use Ibelousov\MathExec\Exceptions\WrongArgumentNumberException;

class BuiltinCollection
{
    protected $builtIn;

    private static $instance = null;

    public static function getInstance(): BuiltinCollection
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function addBuiltin(string $name, callable $method)
    {
        $instance = self::getInstance();

        if(array_key_exists($name, $instance->builtIn)) {
            throw new BuiltinFunctionExistException;
        }

        $instance->builtIn[$name] = new BuiltinFunctionObj($method);
    }

    public function getBuiltin(string $name)
    {
        if(array_key_exists($name, $this->builtIn)) {
            return $this->builtIn[$name];
        }
    }

    private function __construct()
    {
        $this->builtIn = [
            'floor' => new BuiltinFunctionObj(function(...$args) {
                $whole_value = bcmul($args[0][0]->value, '1', 0);

                switch (bccomp($args[0][0]->value, $whole_value, 2))
                {
                    case 0: return new NumberObj($whole_value);
                    case -1: return new NumberObj(bcsub($whole_value, '1', 0));
                    case 1: return new NumberObj(bcmul($whole_value, '1', 0));
                }
            }),
            'ceil' => new BuiltinFunctionObj(function(...$args) {
                $whole_value = bcmul($args[0][0]->value, '1', 0);

                switch (bccomp($args[0][0]->value, $whole_value, 2))
                {
                    case 0: return new NumberObj($whole_value);
                    case -1: return new NumberObj(bcmul($whole_value, '1', 0));
                    case 1: return new NumberObj(bcadd($whole_value, '1', 0));
                }
            }),
            'format' => new BuiltinFunctionObj(function(...$args) {
                if(!isset($args[0][1]))
                    throw new WrongArgumentNumberException();

                return new NumberObj(bcmul($args[0][0]->value, '1.00', $args[0][1]->value));
            })
        ];
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}