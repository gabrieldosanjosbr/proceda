<?php

namespace Proceda;

use Proceda\Error\Validation;

final readonly class Result
{
    public function __construct(
        public Intercambio $intercambio,
        public Validation $validation,
    ) {}
}
