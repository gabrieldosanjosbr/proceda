<?php

namespace Proceda\Builder;

abstract class Builder implements BuilderInterface
{
    private bool $filled = false;

    /**
     * Marks the builder as filled when the condition is true.
     * Once filled, it cannot become unfilled.
     */
    public function setFilled(bool $condition): void
    {
        $this->filled = $this->filled || $condition;
    }

    public function isFilled(): bool
    {
        return $this->filled;
    }
}
