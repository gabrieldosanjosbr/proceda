<?php

namespace Proceda\NotaFiscal;

readonly class Item
{
    public function __construct(
        protected ?string $codigo = null,
        protected ?string $descricao = null,
        protected ?string $cfop = null,
        protected ?string $lote = null,
        protected ?string $dataValidade = null,
        protected ?string $acondicionamento = null,
        protected ?Volume $volume = null,
        protected ?string $idPedidoCliente = null,
        protected ?float  $quantidade = null,
        protected ?float  $quantidadeEntregue = null,
    ) {}

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getCfop(): ?string
    {
        return $this->cfop;
    }

    public function getLote(): ?string
    {
        return $this->lote;
    }

    public function getDataValidade(): ?string
    {
        return $this->dataValidade;
    }

    public function getAcondicionamento(): ?string
    {
        return $this->acondicionamento;
    }

    public function getVolume(): ?Volume
    {
        return $this->volume;
    }

    public function getIdPedidoCliente(): ?string
    {
        return $this->idPedidoCliente;
    }

    public function getQuantidade(): ?float
    {
        return $this->quantidade;
    }

    public function getQuantidadeEntregue(): ?float
    {
        return $this->quantidadeEntregue;
    }
}
