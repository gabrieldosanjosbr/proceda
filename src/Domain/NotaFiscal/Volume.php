<?php

namespace Proceda\NotaFiscal;

readonly class Volume
{
    public function __construct(
        protected ?string $marca = null,
        protected ?string $numeracao = null,
        protected ?string $lacre = null,
    ) {}

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function getNumeracao(): ?string
    {
        return $this->numeracao;
    }

    public function getLacre(): ?string
    {
        return $this->lacre;
    }
}
