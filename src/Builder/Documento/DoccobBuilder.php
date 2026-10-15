<?php

namespace Proceda\Builder\Documento;

use Proceda\Builder\Builder;
use Proceda\Documento\Doccob;
use Proceda\Transportador;

class DoccobBuilder extends Builder
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

    public function build(): Doccob
    {
        return new Doccob(
            identificacao: $this->identificacao,
            qtdDocumentos: $this->qtdDocumentos,
            valorTotal: $this->valorTotal,
            transportador: $this->transportador,
        );
    }
}
