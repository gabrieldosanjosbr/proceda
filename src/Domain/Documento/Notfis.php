<?php

namespace Proceda\Documento;

use Proceda\Embarcador;

final readonly class Notfis implements DocumentoInterface
{
    public function __construct(
        protected ?string $identificacao = null,
        protected ?int    $qtdDocumentos = null,
        protected ?float  $valorTotal = null,
        protected ?float  $pesoBruto = null,
        protected ?float  $totalVolumes = null,
        protected ?float  $pesoDensidadeCubagem = null,
        protected ?float  $valorCobradoCliente = null,
        protected ?float  $valorSeguro = null,
        /** @var Embarcador[] */
        protected array   $embarcadoras = [],
    ) {}

    public function getIdentificacao(): ?string
    {
        return $this->identificacao;
    }

    public function getQtdDocumentos(): ?int
    {
        if ($this->qtdDocumentos > 0) {
            return $this->qtdDocumentos;
        }

        $quantidade = 0;

        foreach ($this->embarcadoras as $embarcadora) {
            foreach ($embarcadora->getDestinatarios() as $destinatario) {
                $quantidade += count($destinatario->getNotasFiscais());
            }
        }

        return $quantidade > 0
            ? $quantidade
            : null;
    }

    public function getValorTotal(): ?float
    {
        return $this->valorTotal;
    }

    public function getPesoBruto(): ?float
    {
        return $this->pesoBruto;
    }

    public function getTotalVolumes(): ?float
    {
        return $this->totalVolumes;
    }

    public function getPesoDensidadeCubagem(): ?float
    {
        return $this->pesoDensidadeCubagem;
    }

    public function getValorCobradoCliente(): ?float
    {
        return $this->valorCobradoCliente;
    }

    public function getValorSeguro(): ?float
    {
        return $this->valorSeguro;
    }

    public function getEmbarcadoras(): array
    {
        return $this->embarcadoras;
    }
}
