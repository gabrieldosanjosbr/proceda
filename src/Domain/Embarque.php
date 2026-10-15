<?php

namespace Proceda;

readonly class Embarque
{
    public function __construct(
        protected ?string $identificacao = null,
    ) {}

    public function getIdentificacao(): ?string
    {
        return $this->identificacao;
    }
}
