<?php

namespace Proceda\Documento;

use Proceda\Transportador;

final readonly class Ocoren implements DocumentoInterface
{
    public function __construct(
        protected ?string $identificacao = null,
        protected ?int $qtdDocumentos = null,
        protected ?Transportador $transportador = null,
    ) {}

    public function getIdentificacao(): ?string
    {
        return $this->identificacao;
    }

    public function getQtdDocumentos(): ?int
    {
        if ($this->qtdDocumentos > 0) {
            return $this->qtdDocumentos;
        }

        $qtdDocumentos = count(
            $this->transportador?->getOcorrencias() ?? [],
        );

        if ($qtdDocumentos === 0) {
            return null;
        }

        return $qtdDocumentos;
    }

    public function getTransportador(): ?Transportador
    {
        return $this->transportador;
    }
}
