<?php

namespace Proceda\Imposto;

use Proceda\Type;

final readonly class Tributo
{
    public function __construct(
        protected Type   $tipo,
        protected ?float $baseCalculo = null,
        protected ?float $aliquota = null,
        protected ?float $valor = null,
        protected ?float $valorRetido = null,
        protected ?Type  $substituicaoTributaria = null,
    ) {}

    public function getTipo(): Type
    {
        return $this->tipo;
    }

    public function getBaseCalculo(): ?float
    {
        return $this->baseCalculo;
    }

    public function getAliquota(): ?float
    {
        return $this->aliquota;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function getValorRetido(): ?float
    {
        return $this->valorRetido;
    }

    public function getSubstituicaoTributaria(): ?Type
    {
        return $this->substituicaoTributaria;
    }
}
