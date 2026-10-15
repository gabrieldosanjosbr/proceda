<?php

namespace Proceda\Error;

class Validation implements \Countable
{
    /**
     * @param list<Error> $errors
     */
    public function __construct(
        public array $errors = [],
    ) {}

    public function add(Error $error): void
    {
        $this->errors[] = $error;
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function hasErrors(): bool
    {
        return count($this) !== 0;
    }

    /**
     * @param callable(Error): bool $callback
     */
    public function find(callable $callback): ?Error
    {
        foreach ($this->errors as $error) {
            if ($callback($error)) {
                return $error;
            }
        }

        return null;
    }
}