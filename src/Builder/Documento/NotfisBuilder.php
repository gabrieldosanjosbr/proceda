<?php

namespace Proceda\Builder\Documento;

use Proceda\Builder\Builder;
use Proceda\Documento\Notfis;
use Proceda\Embarcador;

class NotfisBuilder extends Builder
{
    protected ?string $identificacao = null;
    protected ?int $qtdDocumentos = null;
    protected ?float $valorTotal = null;
    protected ?float $pesoBruto = null;
    protected ?float $totalVolumes = null;
    protected ?float $pesoDensidadeCubagem = null;
    protected ?float $valorCobradoCliente = null;
    protected ?float $valorSeguro = null;
    /** @var Embarcador[] */
    protected array $embarcadoras = [];

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

    public function valorTotal(?float $valorTotal): static
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    public function pesoBruto(?float $pesoBruto = null): static
    {
        $this->pesoBruto = $pesoBruto;

        return $this;
    }

    public function totalVolumes(?float $totalVolumes = null): static
    {
        $this->totalVolumes = $totalVolumes;

        return $this;
    }

    public function pesoDensidadeVolume(?float $pesoDensidadeVolume = null): static
    {
        $this->pesoDensidadeCubagem = $pesoDensidadeVolume;

        return $this;
    }

    public function valorCobradoCliente(?float $valorCobradoCliente = null): static
    {
        $this->valorCobradoCliente = $valorCobradoCliente;

        return $this;
    }

    public function valorSeguro(?float $valorSeguro = null): static
    {
        $this->valorSeguro = $valorSeguro;

        return $this;
    }

    public function addEmbarcador(Embarcador $embarcador): static
    {
        $this->embarcadoras[] = $embarcador;

        return $this;
    }

    public function build(): Notfis
    {
        return new Notfis(
            identificacao: $this->identificacao,
            qtdDocumentos: $this->qtdDocumentos,
            valorTotal: $this->valorTotal,
            pesoBruto: $this->pesoBruto,
            totalVolumes: $this->totalVolumes,
            pesoDensidadeCubagem: $this->pesoDensidadeCubagem,
            valorCobradoCliente: $this->valorCobradoCliente,
            valorSeguro: $this->valorSeguro,
            embarcadoras: $this->embarcadoras,
        );
    }
}
