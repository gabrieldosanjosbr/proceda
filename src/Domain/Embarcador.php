<?php

namespace Proceda;

readonly class Embarcador
{
    public function __construct(
        protected ?Empresa       $empresa = null,
        protected ?string        $dataEmbarque = null,
        protected ?string        $inscricaoEstadualSubstitutoTributario = null,
        protected ?string        $contatoEmergencia = null,
        protected ?Empresa       $localColetaNotaFiscal = null,

        /** @var Destinatario[] */
        protected array          $destinatarios = [],
    ) {}

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function getDataEmbarque(): ?string
    {
        return $this->dataEmbarque;
    }

    public function getInscricaoEstadualSubstitutoTributario(): ?string
    {
        return $this->inscricaoEstadualSubstitutoTributario;
    }

    public function getContatoEmergencia(): ?string
    {
        return $this->contatoEmergencia;
    }

    public function getLocalColetaNotaFiscal(): ?Empresa
    {
        return $this->localColetaNotaFiscal;
    }

    public function getDestinatarios(): array
    {
        return $this->destinatarios;
    }
}
