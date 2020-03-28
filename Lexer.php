<?php

require_once "Token.php";

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
        if($this->readPosition >= mb_strlen($this->input)) {    
            $this->ch = null;
        } else { 
            $this->ch = mb_substr($this->input, $this->readPosition, 1);
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
                    $token = new Token(EQ, $literal); 
                } else {
                    $token = new Token(ASSIGN, $this->ch);
                }
                break;
            case '+': $token = new Token(PLUS, $this->ch); break;
            case '-': $token = new Token(MINUS, $this->ch); break;
            case '^': $token = new Token(POWER, $this->ch); break;
            case '!': 
                if($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(NOT_EQ, $literal);
                } else {
                    $token = new Token(BANG, $this->ch);
                }
                break;
            case '/': 
                    if($this->peekChar() == '/') {
                        $ch = $this->ch;
                        $this->readChar();
                        $literal = $ch . $this->ch;
                        $token = new Token(WDIV, $literal);
                    } else {
                        $token = new Token(SLASH, $this->ch); 
                    }

                    break;
            case '\\': $token = new Token(ROOTS, $this->ch); break;
            case '%': $token = new Token(MODUL, $this->ch); break;
            case '*': $token = new Token(ASTERISK, $this->ch); break;
            case '<': 
                if($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(LT_OR_EQ, $literal);
                } else {
                    $token = new Token(LT, $this->ch);
                }
                break;
            case '>': 
                if($this->peekChar() == '=') {
                    $ch = $this->ch;
                    $this->readChar();
                    $literal = $ch . $this->ch;
                    $token = new Token(GT_OR_EQ, $literal);
                } else {
                    $token = new Token(GT, $this->ch);
                }
                break;
            case '(': $token = new Token(LPAREN, $this->ch); break;
            case ')': $token = new Token(RPAREN, $this->ch); break;
            case '+': $token = new Token(PLUS, $this->ch); break;
            case ',': $token = new Token(COMMA, $this->ch); break;
            case null:
                $token = new Token(EOF, ""); 
                break;
            default:
                if($this->isLetter($this->ch)) {
                    $literal = $this->readIdentifier();
                    $type = $this->lookupIdent($literal);

                    return new Token($type, $literal);
                } else if($this->isDigit($this->ch)){
                    return new Token(NUMBER, $this->readNumber());
                } else {
                    $token = new Token(ILLEGAL, $this->ch);
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

        return mb_substr($this->input, $position, $this->position - $position);
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

        return mb_substr($this->input, $position, $this->position - $position);
    }

    public function readString(): string
    {
        $position = $this->position+1;

        do {
            $this->readChar();
        } while($this->ch != "\"" && $this->ch !== 0);

        return mb_substr($this->input, $position,  $this->position - $position);
    }

    private function isLetter($ch)
    {
        return preg_match('/[А-Яа-яЁёa-zA-Z]/u', $ch);
    }

    private function isDigit($ch)
    {
        return ctype_digit($ch);
    }

    private function lookupIdent(string $ident)
    {
        // if(array_key_exists($ident, KEYWORDS))
        //     return KEYWORDS[$ident];

        return IDENT;
    }

    private function eatWhitespace()
    {
        while(ctype_space($this->ch))
            $this->readChar();
    }

    private function peekChar()
    {
        if($this->position >= mb_strlen($this->input)) {
            return 0;
        } 

        return mb_substr($this->input, $this->readPosition, 1);
    }
}