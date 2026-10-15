<?php

namespace Proceda;

use Proceda\Imposto\Tributo;

readonly class Imposto
{
    public function __construct(
        protected ?Type $condicaoTributaria = null,
        protected ?Type $direitoFiscal = null,
        protected ?Type $incidenciaIcms = null,
        protected ?Type $tratamento = null,
        /** @var Tributo[] */
        protected array $tributos = [],
    ) {}

    public function getCondicaoTributaria(): ?Type
    {
        return $this->condicaoTributaria;
    }

    public function getDireitoFiscal(): ?Type
    {
        return $this->direitoFiscal;
    }

    public function getIncidenciaIcms(): ?Type
    {
        return $this->incidenciaIcms;
    }

    public function getTratamento(): ?Type
    {
        return $this->tratamento;
    }

    public function getTributos(): array
    {
        return $this->tributos;
    }
}
