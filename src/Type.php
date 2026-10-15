<?php

namespace Proceda;

final readonly class Type
{
    public function __construct(
        public string $name,
        public string $value,
        public string $label,
    ) {}
}
