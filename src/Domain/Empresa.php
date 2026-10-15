<?php

namespace Proceda;

readonly class Empresa
{
    public function __construct(
        protected ?string $codigo = null,
        protected ?string $nome = null,
        protected ?string $cnpj = null,
        protected ?string $inscricaoEstadual = null,
        protected ?string $inscricaoMunicipal = null,
        protected ?string $endereco = null,
        protected ?string $bairro = null,
        protected ?string $cep = null,
        protected ?string $cidade = null,
        protected ?string $uf = null,
        protected ?string $areaFrete = null,
        protected ?string $codigoMunicipio = null,
        protected ?string $numeroComunicacao = null,
        protected ?Unidade $filial = null,
    ) {}

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricaoEstadual;
    }

    public function getInscricaoMunicipal(): ?string
    {
        return $this->inscricaoMunicipal;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function getAreaFrete(): ?string
    {
        return $this->areaFrete;
    }

    public function getCodigoMunicipio(): ?string
    {
        return $this->codigoMunicipio;
    }

    public function getNumeroComunicacao(): ?string
    {
        return $this->numeroComunicacao;
    }

    public function getFilial(): ?Unidade
    {
        return $this->filial;
    }
}
