<?php

namespace Proceda;

readonly class Frete
{
    public function __construct(
        protected ?float   $quantidadeVolumes = null,
        protected ?float   $volume = null,
        protected ?float   $pesoBruto = null,
        protected ?float   $pesoCubado = null,
        protected ?float   $pesoDensidadeCubagem = null,
        protected ?float   $valor = null,
        protected ?float   $valorPesoVolume = null,
        protected ?float   $adValorem = null,
        protected ?float   $freteValor = null,
        protected ?float   $valorSecCat = null,
        protected ?float   $valorItr = null,
        protected ?float   $valorDespacho = null,
        protected ?float   $valorPedagio = null,
        protected ?float   $valorAdeme = null,
        protected ?float   $valorDespesasExtrasAdicionais = null,
        protected ?bool    $calculoDiferenciado = null,
        protected ?string  $tabela = null,
        protected ?Type    $condicao = null,
        protected ?Type    $modalidade = null,
        protected ?Imposto $imposto = null,
    ) {}

    public function getQuantidadeVolumes(): ?float
    {
        return $this->quantidadeVolumes;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function getPesoBruto(): ?float
    {
        return $this->pesoBruto;
    }

    public function getPesoCubado(): ?float
    {
        return $this->pesoCubado;
    }

    public function getPesoDensidadeCubagem(): ?float
    {
        return $this->pesoDensidadeCubagem;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function getValorPesoVolume(): ?float
    {
        return $this->valorPesoVolume;
    }

    public function getAdValorem(): ?float
    {
        return $this->adValorem;
    }

    public function getFreteValor(): ?float
    {
        return $this->freteValor;
    }

    public function getValorSecCat(): ?float
    {
        return $this->valorSecCat;
    }

    public function getValorItr(): ?float
    {
        return $this->valorItr;
    }

    public function getValorDespacho(): ?float
    {
        return $this->valorDespacho;
    }

    public function getValorPedagio(): ?float
    {
        return $this->valorPedagio;
    }

    public function getValorAdeme(): ?float
    {
        return $this->valorAdeme;
    }

    public function getValorDespesasExtrasAdicionais(): ?float
    {
        return $this->valorDespesasExtrasAdicionais;
    }

    public function getCalculoDiferenciado(): ?bool
    {
        return $this->calculoDiferenciado;
    }

    public function getTabela(): ?string
    {
        return $this->tabela;
    }

    public function getCondicao(): ?Type
    {
        return $this->condicao;
    }

    public function getModalidade(): ?Type
    {
        return $this->modalidade;
    }

    public function getImposto(): ?Imposto
    {
        return $this->imposto;
    }
}
