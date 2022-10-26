<?php

class ExpressionPreparer
{

    private string $expression;
    private string $error;


    private const OPEN_BRACKET = '(';
    private const CLOSE_BRACKET = ')';

    public function __construct(string $expression)
    {
        $this->expression = $expression;
    }

    public function prepare(): bool
    {
        try {
            preg_match('/-?\d+\s+-?\d+/', $this->expression, $matches);

            if ($matches) {
                throw new DomainException('Между числами нет оператора!');
            }

            $openBracket = substr_count($this->expression, self::OPEN_BRACKET);
            $closeBracket = substr_count($this->expression, self::CLOSE_BRACKET);

            if ($openBracket !== $closeBracket) {
                throw new DomainException('Непарные скобки!');
            }

            $this->expression = preg_replace('/\s/', '', $this->expression);
            $this->expression = str_replace(',', '.', $this->expression);
            preg_match('/[^\d()+\/*-.^]+/', $this->expression, $matches);

            if ($matches) {
                throw new DomainException('Ошибка! В строке могут быть только цифры, скобки, и операторы +, -, *, /, ^');
            }

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        return empty($this->error);
    }

    public function getError(): string
    {
        return $this->error;
    }


    public function getPreparedExpression(): string
    {
        return $this->expression;
    }
}