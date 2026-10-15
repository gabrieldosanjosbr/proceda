<?php

namespace Proceda;

readonly class Nfe
{
    public function __construct(
        protected ?string $codigo = null,
        protected ?string $chaveAcesso = null,
        protected ?string $protocolo  = null,
    ) {}

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function getChaveAcesso(): ?string
    {
        return $this->chaveAcesso;
    }

    public function getProtocolo(): ?string
    {
        return $this->protocolo;
    }
}
