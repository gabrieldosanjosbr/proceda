<?php

namespace Proceda;

readonly class Veiculo
{
    public function __construct(
        protected ?Type   $tipo = null,
        protected ?string $placa = null,
    ) {}

    public function getTipo(): ?Type
    {
        return $this->tipo;
    }

    public function getPlaca(): ?string
    {
        return $this->placa;
    }
}
