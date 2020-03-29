<?php

namespace IvanBelousov\MathExec\Lexer;

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

    public function readChar()
    {
        if($this->readPosition >= strlen($this->input)) {
            $this->ch = null;
        } else { 
            $this->ch = substr($this->input, $this->readPosition, 1);
        }

        $this->position = $this->readPosition;
        $this->readPosition++;
    }

    public function nextToken(): Token 
    {
        $token = null;

        $this->eatWhitespace();

        switch($this->ch)
        {
            case '=': 
                if($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::EQ, $literal);
                } else {
                    $token = new Token(OperatorType::ASSIGN, $this->ch);
                }
                break;
            case '+': $token = new Token(OperatorType::PLUS, $this->ch); break;
            case '-': $token = new Token(OperatorType::MINUS, $this->ch); break;
            case '^': $token = new Token(OperatorType::POWER, $this->ch); break;
            case '!': 
                if($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::NOT_EQ, $literal);
                } else {
                    $token = new Token(OperatorType::BANG, $this->ch);
                }
                break;
            case '/': 
                    if($this->peekChar() == '/') {
                        $ch = $this->ch;
                        $this->readChar();
                        $literal = $ch . $this->ch;
                        $token = new Token(OperatorType::WDIV, $literal);
                    } else {
                        $token = new Token(OperatorType::SLASH, $this->ch);
                    }

                    break;
            case '\\': $token = new Token(OperatorType::ROOTS, $this->ch); break;
            case '%': $token = new Token(OperatorType::MODUL, $this->ch); break;
            case '*': $token = new Token(OperatorType::ASTERISK, $this->ch); break;
            case '<': 
                if($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(OperatorType::LT_OR_EQ, $literal);
                } else {
                    $token = new Token(OperatorType::LT, $this->ch);
                }
                break;
            case '>': 
                if($this->peekChar() == '=') {
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
                if($this->isLetter($this->ch)) {
                    return new Token(OperatorType::IDENT, $this->readIdentifier());
                } else if($this->isDigit($this->ch)){
                    return new Token(OperatorType::NUMBER, $this->readNumber());
                } else {
                    $token = new Token(OperatorType::ILLEGAL, $this->ch);
                }
        }

        $this->readChar();

        return $token;
    }

    public function readIdentifier(): string
    {
        $position = $this->position;

        while($this->isLetter($this->ch)) {
            $this->readChar();
        }

        return substr($this->input, $position, $this->position - $position);
    }

    public function readNumber(): string 
    {
        $position = $this->position;

        while($this->isDigit($this->ch) || $this->ch == '.') {
            $alreadyPoint = $this->ch == '.' ? true : false;

            $this->readChar();
            
            if($alreadyPoint)
                break;
        }

        while($this->isDigit($this->ch)) {
            $this->readChar();
        }

        return substr($this->input, $position, $this->position - $position);
    }

    public function readString(): string
    {
        $position = $this->position+1;

        do {
            $this->readChar();
        } while($this->ch != "\"" && $this->ch !== 0);

        return substr($this->input, $position,  $this->position - $position);
    }

    private function isLetter($ch)
    {
        return preg_match('/[А-Яа-яЁёa-zA-Z]/u', $ch);
    }

    private function isDigit($ch)
    {
        return ctype_digit($ch);
    }

    private function eatWhitespace()
    {
        while(ctype_space($this->ch))
            $this->readChar();
    }

    private function peekChar()
    {
        if($this->position >= strlen($this->input)) {
            return 0;
        } 

        return substr($this->input, $this->readPosition, 1);
    }
}