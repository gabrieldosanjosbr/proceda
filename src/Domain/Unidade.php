<?php

namespace Proceda;

final readonly class Unidade
{
    public function __construct(
        protected ?string $identificacao = null,
        protected ?string $uf = null,
    ) {}

    public function getIdentificacao(): ?string
    {
        return $this->identificacao;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }
}
