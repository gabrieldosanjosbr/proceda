<?php

namespace Proceda\Builder\Documento;

use Proceda\Builder\Builder;
use Proceda\Documento\Ocoren;
use Proceda\Transportador;

class OcorenBuilder extends Builder
{
    protected ?string $identificacao = null;
    protected ?int $qtdDocumentos = null;
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

    public function transportador(Transportador $transportador): static
    {
        $this->transportador = $transportador;

        return $this;
    }

    public function build(): Ocoren
    {
        return new Ocoren(
            identificacao: $this->identificacao,
            qtdDocumentos: $this->qtdDocumentos,
            transportador: $this->transportador,
        );
    }
}
