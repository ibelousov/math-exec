<?php

namespace Ibelousov\MathExec\Lexer;


class Token {

	public $tokenType;
	public $literal;

	public function __construct($tokenType, $literal)
	{
		$this->tokenType = $tokenType;
		$this->literal = $literal;
	}

	public function isEof()
	{
		return $this->tokenType == OperatorType::EOF;
	}
}