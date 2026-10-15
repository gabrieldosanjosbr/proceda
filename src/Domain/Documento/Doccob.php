<?php

namespace Proceda\Documento;

use Proceda\Transportador;

final readonly class Doccob implements DocumentoInterface
{
    public function __construct(
        protected ?string $identificacao = null,
        protected ?int $qtdDocumentos = null,
        protected ?float $valorTotal = null,
        protected ?Transportador $transportador = null,
    ) {}

    public function getIdentificacao(): ?string
    {
        return $this->identificacao;
    }

    public function getQtdDocumentos(): ?int
    {
        return $this->qtdDocumentos;
    }

    public function getValorTotal(): ?float
    {
        return $this->valorTotal;
    }

    public function getTransportador(): ?Transportador
    {
        return $this->transportador;
    }
}
