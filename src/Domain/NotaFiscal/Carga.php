<?php

namespace Proceda\NotaFiscal;

use Proceda\Evento;
use Proceda\Type;

readonly class Carga
{
    public function __construct(
        protected ?Type   $tipo = null,
        protected ?Type   $modalidade = null,
        protected ?string $natureza = null,
        protected ?string $acondicionamento = null,
        protected ?bool   $planoCargaRapida  = null,
        protected ?string $dataEmbarque = null,
        protected ?string $localDesembarque = null,
        protected ?Type   $condicaoPeriodoEntrega = null,
        protected ?Evento $inicioEntrega = null,
        protected ?Evento $fimEntrega = null,
    ) {}

    public function getTipo(): ?Type
    {
        return $this->tipo;
    }

    public function getModalidade(): ?Type
    {
        return $this->modalidade;
    }

    public function getNatureza(): ?string
    {
        return $this->natureza;
    }

    public function getAcondicionamento(): ?string
    {
        return $this->acondicionamento;
    }

    public function getPlanoCargaRapida(): ?bool
    {
        return $this->planoCargaRapida;
    }

    public function getDataEmbarque(): ?string
    {
        return $this->dataEmbarque;
    }

    public function getLocalDesembarque(): ?string
    {
        return $this->localDesembarque;
    }

    public function getCondicaoPeriodoEntrega(): ?Type
    {
        return $this->condicaoPeriodoEntrega;
    }

    public function getInicioEntrega(): ?Evento
    {
        return $this->inicioEntrega;
    }

    public function getFimEntrega(): ?Evento
    {
        return $this->fimEntrega;
    }
}
