<?php

namespace Proceda\Builder;

use Proceda\Type;
use Proceda\Veiculo;

class VeiculoBuilder extends Builder
{
    protected ?Type $tipo = null;
    protected ?string $placa = null;

    public function tipo(?Type $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function placa(?string $placa): static
    {
        $this->placa = $placa;

        return $this;
    }

    public function build(): Veiculo
    {
        return new Veiculo(
            tipo: $this->tipo,
            placa: $this->placa,
        );
    }
}
