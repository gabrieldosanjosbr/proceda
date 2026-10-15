<?php

namespace Proceda;

readonly class Destinatario
{
    public function __construct(
        protected ?string $nome = null,
        protected ?string $documento = null,
        protected ?string $inscricaoEstadual = null,
        protected ?string $inscricaoSuframa = null,
        protected ?string $endereco = null,
        protected ?string $bairro = null,
        protected ?string $cidade = null,
        protected ?string $codigoMunicipio = null,
        protected ?Type   $codigoPais = null,
        protected ?string $cep = null,
        protected ?string $uf = null,
        protected ?string $areaFrete = null,
        protected ?string $numeroComunicacao = null,
        protected ?Type   $estabelecimentoDestino = null,
        protected ?Type   $tipo = null,
        protected ?Destinatario $localEntregaNotaFiscal = null,

        /** @var NotaFiscal[] */
        protected array   $notasFiscais = [],
    ) {}

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricaoEstadual;
    }

    public function getInscricaoSuframa(): ?string
    {
        return $this->inscricaoSuframa;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function getCodigoPais(): ?Type
    {
        return $this->codigoPais;
    }

    public function getCodigoMunicipio(): ?string
    {
        return $this->codigoMunicipio;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function getAreaFrete(): ?string
    {
        return $this->areaFrete;
    }

    public function getNumeroComunicacao(): ?string
    {
        return $this->numeroComunicacao;
    }

    public function getEstabelecimentoDestino(): ?Type
    {
        return $this->estabelecimentoDestino;
    }

    public function getTipo(): ?Type
    {
        return $this->tipo;
    }

    public function getLocalEntregaNotaFiscal(): ?Destinatario
    {
        return $this->localEntregaNotaFiscal;
    }

    public function getNotasFiscais(): array
    {
        return $this->notasFiscais;
    }
}
