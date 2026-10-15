<?php

namespace Proceda\Builder\Documento;

use Proceda\Builder\Builder;
use Proceda\Documento\Conemb;
use Proceda\Transportador;

class ConembBuilder extends Builder
{
    protected ?string $identificacao = null;
    protected ?int $qtdDocumentos = null;
    protected ?float $valorTotal = null;
    protected ?Transportador $transportador = null;

    public function identificacao(string $identificacao): static
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    public function qtdDocumentos(?int $quantidade): static
    {
        $this->qtdDocumentos = $quantidade;

        return $this;
    }

    public function valorTotal(?float $valorTotal): static
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    public function transportador(Transportador $transportador): static
    {
        $this->transportador = $transportador;

        return $this;
    }

    public function build(): Conemb
    {
        return new Conemb(
            identificacao: $this->identificacao,
            qtdDocumentos: $this->qtdDocumentos,
            valorTotal: $this->valorTotal,
            transportador: $this->transportador,
        );
    }
}
