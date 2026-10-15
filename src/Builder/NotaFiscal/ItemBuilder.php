<?php

namespace Proceda\Builder\NotaFiscal;

use Proceda\Builder\Builder;
use Proceda\NotaFiscal\Item;
use Proceda\NotaFiscal\Volume;

class ItemBuilder extends Builder
{
    protected ?string $codigo = null;
    protected ?string $descricao = null;
    protected ?string $cfop = null;
    protected ?string $lote = null;
    protected ?string $acondicionamento = null;
    protected ?string $dataValidade = null;
    protected ?Volume $volume = null;
    protected ?string $idPedidoCliente = null;
    protected ?float $quantidade = null;
    protected ?float $quantidadeEntregue = null;

    public function codigo(?string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function descricao(?string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function cfop(?string $cfop): static
    {
        $this->cfop = $cfop;

        return $this;
    }

    public function lote(?string $lote): static
    {
        $this->lote = $lote;

        return $this;
    }


    public function acondicionamento(?string $acondicionamento): static
    {
        $this->acondicionamento = $acondicionamento;

        return $this;
    }

    public function dataValidade(?string $dataValidade): static
    {
        $this->dataValidade = $dataValidade;

        return $this;
    }

    public function volume(?Volume $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function idPedidoCliente(?string $idPedidoCliente): static
    {
        $this->idPedidoCliente = $idPedidoCliente;

        return $this;
    }

    public function quantidadeVolumes(?float $quantidadeVolumes): static
    {
        $this->quantidade = $quantidadeVolumes;

        return $this;
    }

    public function quantidadeVolumesEntregues(?float $quantidadeVolumesEntregues): static
    {
        $this->quantidadeEntregue = $quantidadeVolumesEntregues;

        return $this;
    }

    public function build(): Item
    {
        return new Item(
            codigo: $this->codigo,
            descricao: $this->descricao,
            cfop: $this->cfop,
            lote: $this->lote,
            dataValidade: $this->dataValidade,
            acondicionamento: $this->acondicionamento,
            volume: $this->volume,
            idPedidoCliente: $this->idPedidoCliente,
            quantidade: $this->quantidade,
            quantidadeEntregue: $this->quantidadeEntregue,
        );
    }
}
