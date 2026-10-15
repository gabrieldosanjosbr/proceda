<?php

namespace Proceda\Builder;

use Proceda\Documento\DocumentoInterface;
use Proceda\Intercambio;

class IntercambioBuilder extends Builder implements RootBuilderInterface
{
    protected ?string $remetente = null;
    protected ?string $destinatario = null;
    protected ?string $data = null;
    protected ?string $hora = null;
    protected ?string $identificacao = null;

    /** @var DocumentoInterface[] */
    protected array $documentos = [];

    public function remetente(?string $remetente): static
    {
        $this->remetente = $remetente;

        return $this;
    }

    public function destinatario(?string $destinatario): static
    {
        $this->destinatario = $destinatario;

        return $this;
    }

    public function data(?string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function hora(?string $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    public function identificacao(?string $identificacao): static
    {
        $this->identificacao = $identificacao;

        return $this;
    }

    public function addDocumento(DocumentoInterface $documento): static
    {
        $this->documentos[] = $documento;

        return $this;
    }

    public function build(): Intercambio
    {
        return new Intercambio(
            remetente: $this->remetente,
            destinatario: $this->destinatario,
            data: $this->data,
            hora: $this->hora,
            identificacao: $this->identificacao,
            documentos: $this->documentos,
        );
    }
}
