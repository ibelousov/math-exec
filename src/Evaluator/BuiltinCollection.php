<?php


namespace Ibelousov\MathExec\Evaluator;


use Ibelousov\MathExec\Exceptions\BuiltinFunctionExistException;

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
                if(strpos('.', $args[0][0]->value))
                    return new NumberObj(bcsub($args[0][0]->value, '0.5', 0));

                return new NumberObj(bcsub($args[0][0]->value, '0', 0));
            }),
            'ceil' => new BuiltinFunctionObj(function(...$args) {
                if(strpos('.', $args[0][0]->value))
                    return new NumberObj(bcadd($args[0][0]->value, '0.5', 0));

                return new NumberObj(bcsub($args[0][0]->value, '0', 0));
            }),
            'format' => new BuiltinFunctionObj(function(...$args) {
                return new NumberObj(bcmul($args[0][0]->value, 1, $args[0][1]->value));
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