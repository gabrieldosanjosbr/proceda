<?php

namespace Proceda\Builder;

use Proceda\Frete;
use Proceda\Imposto;
use Proceda\Type;

class FreteBuilder extends Builder
{
    protected ?float $quantidadeVolumes = null;
    protected ?float $volume = null;
    protected ?float $pesoBruto = null;
    protected ?float $pesoCubado = null;
    protected ?float $pesoDensidadeCubagem = null;
    protected ?float $valor = null;
    protected ?float $valorPesoVolume = null;
    protected ?float $adValorem = null;
    protected ?float $freteValor = null;
    protected ?float $valorSecCat = null;
    protected ?float $valorItr = null;
    protected ?float $valorDespacho = null;
    protected ?float $valorPedagio = null;
    protected ?float $valorAdeme = null;
    protected ?float $valorDespesasExtrasAdicionais = null;
    protected ?bool $calculoDiferenciado = null;
    protected ?string $tabela = null;
    protected ?Type $condicao = null;
    protected ?Type $modalidade = null;
    protected ?Imposto $imposto = null;

    public function quantidadeVolumes(float $value): static
    {
        $this->quantidadeVolumes = $value;
        return $this;
    }
    public function volume(float $value): static
    {
        $this->volume = $value;
        return $this;
    }
    public function pesoBruto(float $value): static
    {
        $this->pesoBruto = $value;
        return $this;
    }
    public function pesoCubado(?float $value): static
    {
        $this->pesoCubado = $value;
        return $this;
    }
    public function pesoDensidadeCubagem(?float $value): static
    {
        $this->pesoDensidadeCubagem = $value;
        return $this;
    }
    public function valor(?float $value): static
    {
        $this->valor = $value;
        return $this;
    }
    public function valorPesoVolume(?float $value): static
    {
        $this->valorPesoVolume = $value;
        return $this;
    }
    public function adValorem(?float $value): static
    {
        $this->adValorem = $value;
        return $this;
    }
    public function freteValor(?float $value): static
    {
        $this->freteValor = $value;
        return $this;
    }
    public function valorSecCat(?float $value): static
    {
        $this->valorSecCat = $value;
        return $this;
    }
    public function valorItr(?float $value): static
    {
        $this->valorItr = $value;
        return $this;
    }
    public function valorDespacho(?float $value): static
    {
        $this->valorDespacho = $value;
        return $this;
    }
    public function valorPedagio(?float $value): static
    {
        $this->valorPedagio = $value;
        return $this;
    }
    public function valorAdeme(?float $value): static
    {
        $this->valorAdeme = $value;
        return $this;
    }
    public function valorDespesasExtrasAdicionais(?float $value): static
    {
        $this->valorDespesasExtrasAdicionais = $value;
        return $this;
    }
    public function tabela(?string $value): static
    {
        $this->tabela = $value;
        return $this;
    }
    public function calculoDiferenciado(?bool $value): static
    {
        $this->calculoDiferenciado = $value;
        return $this;
    }
    public function modalidade(?Type $value): static
    {
        $this->modalidade = $value;
        return $this;
    }
    public function condicao(?Type $value): static
    {
        $this->condicao = $value;
        return $this;
    }
    public function imposto(?Imposto $value): static
    {
        $this->imposto = $value;
        return $this;
    }

    public function build(): Frete
    {
        return new Frete(
            quantidadeVolumes: $this->quantidadeVolumes,
            volume: $this->volume,
            pesoBruto: $this->pesoBruto,
            pesoCubado: $this->pesoCubado,
            pesoDensidadeCubagem: $this->pesoDensidadeCubagem,
            valor: $this->valor,
            valorPesoVolume: $this->valorPesoVolume,
            adValorem: $this->adValorem,
            freteValor: $this->freteValor,
            valorSecCat: $this->valorSecCat,
            valorItr: $this->valorItr,
            valorDespacho: $this->valorDespacho,
            valorPedagio: $this->valorPedagio,
            valorAdeme: $this->valorAdeme,
            valorDespesasExtrasAdicionais: $this->valorDespesasExtrasAdicionais,
            calculoDiferenciado: $this->calculoDiferenciado,
            tabela: $this->tabela,
            condicao: $this->condicao,
            modalidade: $this->modalidade,
            imposto: $this->imposto,
        );
    }
}
