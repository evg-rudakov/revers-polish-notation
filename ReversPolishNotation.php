<?php

class ReversPolishNotation
{
    private const UNARY_MINUS = '~';
    private const MINUS = '-';
    private const PLUS = '+';
    private const DIVISION = '/';
    private const MULTIPLICATION = '*';
    private const EXPONENTIATION = '^';

    private const PRIORITY = [
        self::PLUS => 2,
        self::MINUS => 2,
        self::MULTIPLICATION => 3,
        self::DIVISION => 3,
        self::EXPONENTIATION => 4,
        self::UNARY_MINUS => 5
    ];

    private const RIGHT_ASSOCIATIVE_EXPRESSION = [
        self::EXPONENTIATION, self::UNARY_MINUS
    ];

    private array $stack = [];
    private array $array = [];
    private string $expression = '';

    private float $result;
    private ExpressionValidator $validator;

    public function __construct()
    {
        $this->validator = new ExpressionValidator();
    }


    private function setExpression(string $expression): void
    {
        $this->expression = $expression;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function calculate(string $expression): void
    {
        if ($this->validator->validate($expression)) {
            $this->setExpression($expression);
        } else {
            throw new DomainException($this->validator->getError());
        }

        $this->result = $this->prepareArray()->calculateFromArray();
    }

    public function getResult(): float
    {
        return $this->result;
    }

    private function calculateFromArray(): float
    {
        $stack = [];
        var_dump($this->array);
        foreach ($this->array as $item) {
            if (is_float($item)) {
                $stack[] = $item;
                continue;
            }

            if ($item === self::UNARY_MINUS) {
                $last = array_pop($stack);
                if (!is_numeric($last)) {
                    throw new DomainException('Invalid Expression');
                }
                $stack[] = 0 - $last;
                continue;
            }

            $right = array_pop($stack) ?? null;
            $left = array_pop($stack) ?? null;

            if (is_null($right) || is_null($left)) {
                throw new DomainException('Invalid Expression');
            }

            $stack[] = $this->makeOperator($left, $right, $item);
        }

        return $stack[0];
    }


    private function makeOperator($left, $right, $operator)
    {
        switch ($operator) {
            case self::MINUS:
                return $left - $right;
            case self::PLUS:
                return $left + $right;
            case self::MULTIPLICATION:
                return $left * $right;
            case self::EXPONENTIATION:
                return $left ** $right;
            case self::DIVISION:
                if ($right == 0) {
                    throw new DomainException('Division by zero');
                }
                return $left / $right;
            default:
                throw new DomainException('Unknown operator ' . $operator);
        }
    }

    private function prepareArray(): self
    {
        var_dump($this->expression);
        $length = strlen($this->expression) - 1;
        $number = null;

        for ($i = 0; $i <= $length; $i++) {
            $item = $this->expression[$i];
            $left = $i === 0 ? null : $this->expression[$i - 1];
            $right = $i === $length ? null : $this->expression[$i + 1];

            if ($item === '-') {
                $operators = [self::PLUS, self::MULTIPLICATION, self::EXPONENTIATION, self::MINUS, self::DIVISION];
                if ($left === null || in_array($left, $operators)) {
                    $item = self::UNARY_MINUS;
                }
            }

            if (is_numeric($item) || $item === '.') {
                if ($item === '.') {
                    if (!is_numeric($left) || !is_numeric($right)) {
                        throw new DomainException('Invalid fractional expression');
                    }
                }
                $number .= $item;

                if (!is_numeric($right)) {
                    $this->array[] = (float)$number;
                    $number = null;
                }
                continue;
            }

            if (in_array($item, array_keys(self::PRIORITY))) {
                $this->addToStackAndPushFromStack($item);
            }
        }

        while ($this->stack) {
            $this->array[] = array_pop($this->stack);
        }

        return $this;
    }

    private function addToStackAndPushFromStack(string $operator)
    {
        if (!$this->stack) {
            $this->stack[] = $operator;
            return;
        }

        $stack = array_reverse($this->stack);
        foreach ($stack as $key => $item) {
            if (in_array($item, self::RIGHT_ASSOCIATIVE_EXPRESSION) && $item === $operator) {
                break;
            }

            if (self::PRIORITY[$item] < self::PRIORITY[$operator]) {
                break;
            }

            $this->array[] = $item;
            unset($stack[$key]);
        }

        $this->stack = array_reverse($stack);
        $this->stack[] = $operator;
    }
}

