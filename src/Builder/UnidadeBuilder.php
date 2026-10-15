<?php

namespace Proceda\Builder;

use Proceda\Unidade;

class UnidadeBuilder extends Builder
{
    protected ?string $identificacao = null;
    protected ?string $uf = null;

    public function identificacao(?string $identificacao): static
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    public function uf(?string $uf): static
    {
        $this->uf = $uf;

        return $this;
    }

    public function build(): Unidade
    {
        return new Unidade(
            identificacao: $this->identificacao,
            uf: $this->uf,
        );
    }
}
