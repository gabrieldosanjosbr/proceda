<?php

namespace Proceda\Conhecimento;

readonly class Cte
{
    public function __construct(
        protected ?string $modelo = null,
        protected ?string $chaveAcesso = null,
        protected ?string $protocolo  = null,
    ) {}

    public function getModelo(): ?string
    {
        return $this->modelo;
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
