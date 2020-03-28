<?php

require_once 'Obj.php';

class Environment
{
    public $store;

    public function __construct()
    {
        $this->store = [];
    }

    public function get($name)
    {
        if(array_key_exists($name, $this->store))
            return [$this->store[$name], true];
         
         return [null, false];
    }

    public function set($name, $val)
    {
        $this->store[$name] = $val;

        return $val;
    }
}

class EnclosedEnvironment extends Environment
{
    public $outer;

    public function __construct($outer)
    {
        parent::__construct();

        $this->outer = $outer;
    }

    public function get($name)
    {
        if(!array_key_exists($name, $this->store))
            return $outer->Get($name);

        return [$this->store[$name], true];
    }
}