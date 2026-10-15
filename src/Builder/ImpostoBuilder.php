<?php

namespace Proceda\Builder;

use Proceda\Imposto;
use Proceda\Imposto\Tributo;
use Proceda\Type;

class ImpostoBuilder extends Builder
{
    protected ?Type $condicaoTributaria = null;
    protected ?Type $direitoFiscal = null;
    protected ?Type $incidenciaIcms = null;
    protected ?Type $tratamento = null;

    /** @var Tributo[] */
    protected array $tributos = [];

    public function condicaoTributaria(?Type $value): static
    {
        $this->condicaoTributaria = $value;

        return $this;
    }

    public function direitoFiscal(?Type $value): static
    {
        $this->direitoFiscal = $value;

        return $this;
    }

    public function incidenciaIcms(?Type $value): static
    {
        $this->incidenciaIcms = $value;

        return $this;
    }

    public function tratamento(?Type $tratamento): static
    {
        $this->tratamento = $tratamento;

        return $this;
    }

    public function addTributo(Tributo $value): static
    {
        $this->tributos[] = $value;

        return $this;
    }

    public function build(): Imposto
    {
        return new Imposto(
            condicaoTributaria: $this->condicaoTributaria,
            direitoFiscal: $this->direitoFiscal,
            incidenciaIcms: $this->incidenciaIcms,
            tratamento: $this->tratamento,
            tributos: $this->tributos,
        );
    }
}
