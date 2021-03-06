<?php

namespace Ibelousov\MathExec\Lexer;

use Ibelousov\MathExec\Exceptions\UnknownNumberFormatException;
use Ibelousov\MathExec\Exceptions\WrongTokenException;

class Lexer
{
    public $input;
    public $position;
    public $readPosition;
    public $ch;

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->readPosition = 0;
        $this->position = 0;

        $this->readChar();
    }

    public function nextToken(): Token
    {
        $token = null;

        $this->eatWhitespace();

        switch ($this->ch) {
            case '=':
                if ($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::EQ, $literal);
                } else {
                    throw new WrongTokenException("After = should be " . $this->peekChar());
                }
                break;
            case '+': $token = new Token(OperatorType::PLUS, $this->ch); break;
            case '-': $token = new Token(OperatorType::MINUS, $this->ch); break;
            case '^': $token = new Token(OperatorType::POWER, $this->ch); break;
            case '!':
                if ($this->peekChar() === '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::NOT_EQ, $literal);
                } else {
                    $token = new Token(OperatorType::BANG, $this->ch);
                }
                break;
            case '/':
                    if ($this->peekChar() === '/') {
                        $ch = $this->ch;
                        $this->readChar();
                        $literal = $ch . $this->ch;
                        $token = new Token(OperatorType::WDIV, $literal);
                    } else {
                        $token = new Token(OperatorType::SLASH, $this->ch);
                    }

                    break;
            case '\\': $token = new Token(OperatorType::ROOTS, $this->ch); break;
            case '%':  $token = new Token(OperatorType::MODUL, $this->ch); break;
            case '*':  $token = new Token(OperatorType::ASTERISK, $this->ch); break;
            case '<':
                if ($this->peekChar() === '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::LT_OR_EQ, $literal);
                } else {
                    $token = new Token(OperatorType::LT, $this->ch);
                }
                break;
            case '>':
                if ($this->peekChar() === '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::GT_OR_EQ, $literal);
                } else {
                    $token = new Token(OperatorType::GT, $this->ch);
                }
                break;
            case '(': $token = new Token(OperatorType::LPAREN, $this->ch); break;
            case ')': $token = new Token(OperatorType::RPAREN, $this->ch); break;
            case ',': $token = new Token(OperatorType::COMMA, $this->ch); break;
            case null:
                $token = new Token(OperatorType::EOF, "");
                break;
            default:
                if ($this->isLetter($this->ch)) {
                    return new Token(OperatorType::IDENT, $this->readIdentifier());
                } elseif ($this->isDigit($this->ch)) {
                    return new Token(OperatorType::NUMBER, $this->readNumber());
                }
                throw new WrongTokenException();
        }

        $this->readChar();

        return $token;
    }

    protected function readChar()
    {
        if ($this->readPosition >= strlen($this->input)) {
            $this->ch = null;
        } else {
            $this->ch = $this->input[$this->readPosition];
        }

        $this->position = $this->readPosition;
        $this->readPosition++;
    }

    protected function readIdentifier(): string
    {
        $position = $this->position;

        while ($this->isLetter($this->ch)) {
            $this->readChar();
        }

        return substr($this->input, $position, $this->position - $position);
    }

    protected function readNumber(): string
    {
        $position = $this->position;

        while ($this->isDigit($this->ch) || $this->ch == '.') {
            $alreadyPoint = $this->ch == '.' ? true : false;

            $this->readChar();
            
            if ($alreadyPoint) {
                break;
            }
        }

        while ($this->isDigit($this->ch)) {
            $this->readChar();
        }

        $number = substr($this->input, $position, $this->position - $position);

        if ($this->ch == 'E') {
            if (!$this->peekChar() == '-' || !$this->peekChar() == '+') {
                throw new UnknownNumberFormatException();
            }

            $this->readChar();
            $sign = $this->ch;

            if (!is_numeric($this->peekChar())) {
                throw new UnknownNumberFormatException();
            }

            $this->readChar();
            $positionExpo = $this->position;

            while ($this->isDigit($this->ch)) {
                $this->readChar();
            }

            $power = substr($this->input, $positionExpo, $this->position - $positionExpo);

            if ($sign == '+') {
                $number = bcmul($number, bcpow('10', $power), 64);
            } else {
                $number = bcdiv($number, bcpow('10', $power), 64);
            }
        }

        return $number;
    }

    protected function isLetter($ch)
    {
        return ctype_alpha($ch);
    }

    protected function isDigit($ch)
    {
        return ctype_digit($ch);
    }

    protected function eatWhitespace()
    {
        while (ctype_space($this->ch)) {
            $this->readChar();
        }
    }

    protected function peekChar()
    {
        if ($this->readPosition >= strlen($this->input)) {
            return 0;
        }

        return $this->input[$this->readPosition];
    }
}
