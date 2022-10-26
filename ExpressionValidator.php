<?php

class ExpressionValidator
{

    private string $error;

    public function validate(string $expression): bool
    {
        try {
            $expression = preg_replace('/\s/', '', $expression);
            $expression = str_replace(',', '.', $expression);
            preg_match('/[^\d()+\/*-.^]+/', $expression, $matches);

            if ($matches) {
                throw new DomainException('String can only contain numbers, brackets, and operators such as +, -, *, /, ^');
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
}