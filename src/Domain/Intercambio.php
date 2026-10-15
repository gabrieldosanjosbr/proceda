<?php

namespace Proceda;

use Proceda\Documento\DocumentoInterface;

readonly class Intercambio
{
    public function __construct(
        protected ?string $remetente = null,
        protected ?string $destinatario = null,
        protected ?string $data = null,
        protected ?string $hora = null,
        protected ?string $identificacao  = null,
        /** @var DocumentoInterface[] */
        protected array $documentos = [],
    ) {}

    public function getRemetente(): ?string
    {
        return $this->remetente;
    }

    public function getDestinatario(): ?string
    {
        return $this->destinatario;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getHora(): ?string
    {
        return $this->hora;
    }

    public function getIdentificacao(): ?string
    {
        return $this->identificacao;
    }

    public function getDocumentos(): array
    {
        return $this->documentos;
    }
}
