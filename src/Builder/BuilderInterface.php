<?php

namespace Proceda\Builder;

/**
 * @template T of object
 */
interface BuilderInterface
{
    /**
     * @return T
     */
    public function build(): object;
    public function setFilled(bool $condition): void;
    public function isFilled(): bool;
}
