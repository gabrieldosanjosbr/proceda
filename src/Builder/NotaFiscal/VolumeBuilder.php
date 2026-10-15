<?php

namespace Proceda\Builder\NotaFiscal;

use Proceda\Builder\Builder;
use Proceda\NotaFiscal\Volume;

class VolumeBuilder extends Builder
{
    protected ?string $marca = null;
    protected ?string $numeracao = null;
    protected ?string $lacre = null;

    public function marca(?string $marca): static
    {
        $this->marca = $marca;

        return $this;
    }

    public function numeracao(?string $numeracao): static
    {
        $this->numeracao = $numeracao;

        return $this;
    }

    public function lacre(?string $lacre): static
    {
        $this->lacre = $lacre;

        return $this;
    }

    public function build(): Volume
    {
        return new Volume(
            marca: $this->marca,
            numeracao: $this->numeracao,
            lacre: $this->lacre,
        );
    }
}
