<?php

namespace Proceda\Builder\NotaFiscal;

use Proceda\Builder\Builder;
use Proceda\Evento;
use Proceda\NotaFiscal\Carga;
use Proceda\Type;

class CargaBuilder extends Builder
{
    protected ?Type   $tipo = null;
    protected ?Type   $modalidade = null;
    protected ?string $natureza = null;
    protected ?string $acondicionamento = null;
    protected ?bool   $planoCargaRapida  = null;
    protected ?string $dataEmbarque = null;
    protected ?string $localDesembarque = null;
    protected ?Type   $condicaoPeriodoEntrega = null;
    protected ?Evento $inicioEntrega = null;
    protected ?Evento $fimEntrega = null;

    public function tipo(?Type $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function modalidade(?Type $modalidade): static
    {
        $this->modalidade = $modalidade;

        return $this;
    }

    public function natureza(string $natureza): static
    {
        $this->natureza = $natureza;

        return $this;
    }

    public function acondicionamento(?string $acondicionamento): static
    {
        $this->acondicionamento = $acondicionamento;

        return $this;
    }

    public function planoCargaRapida(?bool $planoCargaRapida): static
    {
        $this->planoCargaRapida = $planoCargaRapida;

        return $this;
    }

    public function dataEmbarque(?string $dataEmbarque): static
    {
        $this->dataEmbarque = $dataEmbarque;

        return $this;
    }

    public function localDesembarque(?string $desembarque): static
    {
        $this->localDesembarque = $desembarque;

        return $this;
    }

    public function condicaoPeriodoEntrega(?Type $condicao): static
    {
        $this->condicaoPeriodoEntrega = $condicao;

        return $this;
    }

    public function inicioEntrega(?Evento $evento): static
    {
        $this->inicioEntrega = $evento;

        return $this;
    }

    public function fimEntrega(?Evento $evento): static
    {
        $this->fimEntrega = $evento;

        return $this;
    }

    public function build(): Carga
    {
        return new Carga(
            tipo: $this->tipo,
            modalidade: $this->modalidade,
            natureza: $this->natureza,
            acondicionamento: $this->acondicionamento,
            planoCargaRapida: $this->planoCargaRapida,
            dataEmbarque: $this->dataEmbarque,
            localDesembarque: $this->localDesembarque,
            condicaoPeriodoEntrega: $this->condicaoPeriodoEntrega,
            inicioEntrega: $this->inicioEntrega,
            fimEntrega: $this->fimEntrega,
        );
    }
}
